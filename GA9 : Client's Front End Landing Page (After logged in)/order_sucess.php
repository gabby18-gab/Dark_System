<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "fabulous_finds";

$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    die("Error: User not logged in. Please log in first.");
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Get form data
$totalAmount = mysqli_real_escape_string($conn, $_POST['total'] ?? '');
$paymentMethod = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');
$shippingAddress = mysqli_real_escape_string($conn, $_POST['address'] ?? '');

$userID = $_SESSION['userID'];
$orderDate = date("Y-m-d H:i:s");

$orderProcessed = false;
$orderID = null;
$error = null;

// Start transaction
mysqli_begin_transaction($conn);

try {
    // 1️⃣ Get seller ID from the first product in cart
    $firstProductID = array_key_first($_SESSION['cart']);
    $getSeller = mysqli_query($conn, "SELECT SellerID FROM product WHERE ProductID = '$firstProductID'");
    
    if (!$getSeller) {
        throw new Exception("Error getting seller: " . mysqli_error($conn));
    }
    
    $sellerRow = mysqli_fetch_assoc($getSeller);
    
    if (!$sellerRow) {
        throw new Exception("No seller found for product ID: $firstProductID");
    }
    
    $sellerID = $sellerRow['SellerID'];

    // 2️⃣ Verify user exists in database
    $checkUser = mysqli_query($conn, "SELECT UserID FROM user WHERE UserID = '$userID'");
    if (mysqli_num_rows($checkUser) == 0) {
        throw new Exception("User ID $userID does not exist in database");
    }

    // 3️⃣ Create Order
    $insertOrder = "INSERT INTO orders (UserID, SellerID, OrderDate, Status) 
                    VALUES ('$userID', '$sellerID', '$orderDate', 'Pending')";
    
    if (!mysqli_query($conn, $insertOrder)) {
        throw new Exception("Error creating order: " . mysqli_error($conn));
    }

    // Get the generated orderID
    $orderID = mysqli_insert_id($conn);

    if ($orderID == 0) {
        throw new Exception("Failed to get order ID");
    }

    // 4️⃣ Insert all order items
    foreach ($_SESSION['cart'] as $productID => $qty) {
        $productID = mysqli_real_escape_string($conn, $productID);
        $qty = mysqli_real_escape_string($conn, $qty);
        
        // Verify product exists
        $checkProduct = mysqli_query($conn, "SELECT ProductID FROM product WHERE ProductID = '$productID'");
        if (mysqli_num_rows($checkProduct) == 0) {
            throw new Exception("Product ID $productID does not exist");
        }
        
        $insertOrderDetails = mysqli_query($conn, 
            "INSERT INTO orderdetails (OrderID, ProductID, Quantity) 
             VALUES ('$orderID', '$productID', '$qty')");
        
        if (!$insertOrderDetails) {
            throw new Exception("Error adding order details: " . mysqli_error($conn));
        }
    }

    // 5️⃣ Insert payment record
    $insertPayment = "INSERT INTO payment (OrderID, Amount, PaymentMethod, PaymentDate)
                      VALUES ('$orderID', '$totalAmount', '$paymentMethod', '$orderDate')";
    
    if (!mysqli_query($conn, $insertPayment)) {
        throw new Exception("Error processing payment: " . mysqli_error($conn));
    }

    // 6️⃣ Commit transaction
    mysqli_commit($conn);
    
    // 7️⃣ Clear cart
    unset($_SESSION['cart']);
    
    $orderProcessed = true;

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    $error = $e->getMessage();
    error_log("Order processing failed: " . $error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $orderProcessed ? 'Order Successful' : 'Order Failed'; ?> - Fabulous Finds</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png" />
    <link rel="stylesheet" href="../assets/css/order-success.css">
</head>
<body>
    <div class="result-container">
        <?php if ($orderProcessed): ?>
            <!-- SUCCESS MESSAGE -->
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h1 class="success-title">Order Successful!</h1>
            
            <p class="result-message">
                Thank you for your purchase! Your order has been successfully placed and is being processed.
            </p>
            
            <?php if ($orderID): ?>
            <div class="order-id">
                <strong>Order ID:</strong> #<?php echo htmlspecialchars($orderID); ?>
            </div>
            <?php endif; ?>
            
            <p class="result-message">
                You will receive an email confirmation shortly. You can track your order in your account.
            </p>
            
            <a href="home.php" class="btn-home">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
            
            <div class="countdown">
                Redirecting to home in <span id="countdown">5</span> seconds...
            </div>

            <script>
                // Countdown timer for automatic redirect
                let countdown = 5;
                const countdownElement = document.getElementById('countdown');
                const countdownInterval = setInterval(function() {
                    countdown--;
                    countdownElement.textContent = countdown;
                    
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        window.location.href = 'home.php';
                    }
                }, 1000);

                // Redirect immediately if user clicks anywhere
                document.addEventListener('click', function() {
                    window.location.href = 'home.php';
                });
            </script>

        <?php else: ?>
            <!-- ERROR MESSAGE -->
            <div class="error-icon">
                <i class="fas fa-times"></i>
            </div>
            
            <h1 class="error-title">Order Failed</h1>
            
            <p class="result-message">
                We encountered an issue while processing your order.
            </p>
            
            <?php if ($error): ?>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <p class="result-message">
                Please try again or contact our support team if the problem persists.
            </p>
            
            <div>
                <a href="checkout.php" class="btn-retry">
                    <i class="fas fa-redo me-2"></i>Try Again
                </a>
                <a href="home.php" class="btn-home">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>