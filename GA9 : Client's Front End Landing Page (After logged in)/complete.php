<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "fabulous_finds";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify the order belongs to the logged-in user
    $verify_sql = "SELECT OrderID FROM orders WHERE OrderID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $verify_sql);
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt);
    $verify_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Update order status to Complete
        $update_sql = "UPDATE orders SET Status = 'Completed' WHERE OrderID = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: orderlist.php");
exit();
?>