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

// ✅ Initialize cart
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// ✅ Handle remove item
if(isset($_POST['remove_item'])){
    $id = $_POST['product_id'];
    unset($_SESSION['cart'][$id]);
}

// ✅ Handle update quantity via AJAX or form submission
if(isset($_POST['update_qty'])){
    $id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    
    // Get product stock
    $stock_query = "SELECT StockQuantity FROM product WHERE ProductID = $id";
    $stock_result = mysqli_query($conn, $stock_query);
    $stock_row = mysqli_fetch_assoc($stock_result);
    $max_stock = $stock_row['StockQuantity'];
    
    if($qty > 0 && $qty <= $max_stock) {
        $_SESSION['cart'][$id] = $qty;
    } elseif($qty > $max_stock) {
        $_SESSION['cart'][$id] = $max_stock; // Set to max stock
    } else {
        unset($_SESSION['cart'][$id]);
    }
}

// ✅ Get cart product details with stock information
$cart_items = [];
$total = 0;

if(!empty($_SESSION['cart'])){
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT ProductID, ProductName, Price, image, StockQuantity FROM product WHERE ProductID IN ($ids)";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)){
        $id = $row['ProductID'];
        $qty = $_SESSION['cart'][$id];
        $subtotal = $row['Price'] * $qty;
        $total += $subtotal;
        $cart_items[] = [
            'id' => $id,
            'name' => $row['ProductName'],
            'price' => $row['Price'],
            'image' => $row['image'],
            'qty' => $qty,
            'stock' => $row['StockQuantity'],
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Fabulous Finds</title>
  <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/cart.css">
</head>
<body>

<header>
  <div class="top-header">
    <div class="logo">Fabulous Finds</div>

    <div class="userlinks">
      <a href="cart.php" class="icon-link">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span class="cart-badge"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>
      </a>

      <a href="orderlist.php" class="icon-link">
        <span class="material-symbols-outlined">local_shipping</span>
      </a>

      <!-- 🔸 Profile Dropdown -->
      <div class="profile-dropdown">
        <button id="profile-btn">
          <span class="material-symbols-outlined">account_circle</span>
        </button>
        <div class="dropdown-menu" id="dropdown-menu">
          <a href="#">Edit Profile</a>
          <a href="#">Add Address</a>
          <a href="#">Settings</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <nav class="menu">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="contact.php">Contact</a>
  </nav>
</header>


<main>
  <h1 style="text-align:center; margin:30px 0;">🛒 Your Shopping Cart</h1>

  <?php if(empty($cart_items)): ?>
    <p style="text-align:center; font-size:1.1rem;">Your cart is empty 😔</p>
  <?php else: ?>
  <div class="cart-container">
    <table>
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Action</th>
      </tr>
      <?php foreach($cart_items as $item): ?>
      <tr data-product-id="<?php echo $item['id']; ?>">
        <td class="product-info">
          <img src="../assets/img/<?php echo $item['image']; ?>" alt="">
          <span><?php echo $item['name']; ?></span>
        </td>
        <td class="price">₱<?php echo number_format($item['price'], 2); ?></td>
        <td>
          <form method="post" class="qty-form">
            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
            <input type="number" name="quantity" value="<?php echo $item['qty']; ?>" min="1" 
                   max="<?php echo $item['stock']; ?>"
                   class="quantity-input" 
                   data-price="<?php echo $item['price']; ?>"
                   data-stock="<?php echo $item['stock']; ?>">
            <div class="stock-info">Available: <?php echo $item['stock']; ?></div>
          </form>
        </td>
        <td class="subtotal">₱<?php echo number_format($item['subtotal'], 2); ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
            <button type="submit" name="remove_item" class="btn-remove">Remove</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <div class="cart-summary">
      <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
    </div>
  </div>
  <?php endif; ?>
</main>

<script>
  // 🔸 Dropdown toggle logic
  const profileBtn = document.getElementById("profile-btn");
  const dropdownMenu = document.getElementById("dropdown-menu");

  profileBtn.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show");
  });

  // 🔸 Close dropdown kapag pinindot sa labas
  window.addEventListener("click", (e) => {
    if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.remove("show");
    }
  });

  // 🔸 Auto-update price and cart when quantity changes
  document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(input => {
      input.addEventListener('change', function() {
        validateQuantity(this);
        updateProductPrice(this);
        updateCartSession(this);
      });
      
      // Optional: Also update on input for real-time feedback
      input.addEventListener('input', function() {
        validateQuantity(this);
        updateProductPrice(this);
      });
    });
  });

  function validateQuantity(input) {
    const quantity = parseInt(input.value) || 0;
    const maxStock = parseInt(input.dataset.stock);
    const min = parseInt(input.min);
    
    if (quantity < min) {
      input.value = min;
      showMessage('Quantity cannot be less than ' + min, 'error');
    } else if (quantity > maxStock) {
      input.value = maxStock;
      showMessage('Only ' + maxStock + ' items available in stock', 'warning');
    }
  }

  function updateProductPrice(input) {
    const quantity = parseInt(input.value) || 0;
    const price = parseFloat(input.dataset.price);
    const row = input.closest('tr');
    const subtotalElement = row.querySelector('.subtotal');
    
    // Calculate new subtotal
    const newSubtotal = quantity * price;
    
    // Update subtotal display
    subtotalElement.textContent = '₱' + newSubtotal.toFixed(2);
    
    // Update cart total
    updateCartTotal();
  }

  function updateCartTotal() {
    const subtotalElements = document.querySelectorAll('.subtotal');
    let newTotal = 0;
    
    subtotalElements.forEach(element => {
      const subtotalText = element.textContent.replace('₱', '').replace(',', '');
      newTotal += parseFloat(subtotalText);
    });
    
    // Update total display
    document.getElementById('cart-total').textContent = newTotal.toFixed(2);
  }

  function updateCartSession(input) {
    const form = input.closest('form');
    const formData = new FormData(form);
    
    // Add the update_qty action
    formData.append('update_qty', '1');
    
    // Submit the form silently
    fetch('cart.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      // Update cart badge if needed
      updateCartBadge();
    })
    .catch(error => {
      console.error('Error updating cart:', error);
    });
  }

  function updateCartBadge() {
    // You can implement this to update the cart badge count
    // This would require additional AJAX to get the total count
    // For now, we'll just reload the page to reflect changes
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  }

  function showMessage(message, type) {
    // Remove existing messages
    const existingMessage = document.querySelector('.cart-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `cart-message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 20px;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      z-index: 10000;
      animation: slideIn 0.3s ease;
      ${type === 'error' ? 'background: #c75c5c;' : 'background: #e67e22;'}
    `;
    
    document.body.appendChild(messageDiv);
    
    // Remove message after 3 seconds
    setTimeout(() => {
      messageDiv.remove();
    }, 3000);
  }
</script>

</body>
</html>