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

// ✅ Handle Add to Cart with stock validation
$message = "";
$remainingStock = [];

if(isset($_POST['add_to_cart'])){
    $productID = $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];
    if($quantity < 1) $quantity = 1;

    // Fetch stock from database
    $stockQuery = "SELECT StockQuantity FROM product WHERE ProductID = '$productID'";
    $stockResult = mysqli_query($conn, $stockQuery);
    $row = mysqli_fetch_assoc($stockResult);
    $availableStock = (int) $row['StockQuantity'];

    // Current quantity in cart
    $currentQtyInCart = isset($_SESSION['cart'][$productID]) ? $_SESSION['cart'][$productID] : 0;
    $totalRequestedQty = $currentQtyInCart + $quantity;

    if($totalRequestedQty > $availableStock){
        $message = "❌ Cannot add $quantity item(s). Only ".($availableStock - $currentQtyInCart)." left in stock.";
        $remainingStock[$productID] = $availableStock - $currentQtyInCart;
    } else {
        $_SESSION['cart'][$productID] = $totalRequestedQty;
        $message = "✅ Product added to cart!";
        $remainingStock[$productID] = $availableStock - $totalRequestedQty;
    }
}

// Fetch remaining stock for all products
$query = "SELECT ProductID, StockQuantity FROM product";
$resultStock = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($resultStock)){
    $pid = $row['ProductID'];
    $remainingStock[$pid] = $row['StockQuantity'] - (isset($_SESSION['cart'][$pid]) ? $_SESSION['cart'][$pid] : 0);
}

$cart_count = array_sum($_SESSION['cart']);

// ✅ Fetch products
$query = "SELECT ProductID, ProductName, Category, Price, StockQuantity, image FROM product";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop - Fabulous Finds</title>
<link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<style>
body { background: #fff9e9; color: #333; font-family: Arial, sans-serif; margin:0; padding:0; }
header { background: #fff; border-bottom: 1px solid #ddd; padding: 15px 5%; }
.top-header { display: flex; align-items: center; gap: 20px; }
.logo { font-weight: bold; font-size: 1.5rem; color: #333; flex-shrink: 0; }
.search-bar { position: relative; flex: 1; min-width: 150px; max-width: 550px; }
.search-bar input { width: 100%; padding: 8px 12px 8px 35px; border: 1px solid #ccc; border-radius: 20px; }
.search-bar .material-symbols-outlined { position: absolute; top: 50%; left: 10px; transform: translateY(-50%); font-size: 20px; color: #888; }
.userlinks { display: flex; align-items: center; gap: 15px; margin-left: auto; }
.userlinks a { text-decoration: none; color: #555; }
.userlinks a:hover { color: #c19a6b; }
.material-symbols-outlined { font-size: 22px; vertical-align: middle; }
.menu { text-align: left; margin-top: 12px; border-top: 1px solid #eee; padding-top: 10px; }
.menu a { text-decoration: none; color: #555; margin-right: 20px; font-size: 0.95rem; font-weight: 500; }
.menu a:hover { color: #c19a6b; }
.profile-dropdown { position: relative; }
.profile-dropdown button { background: none; border: none; cursor: pointer; color: #555; padding: 5px; }
.dropdown-menu { display: none; position: absolute; right: 0; top: 35px; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); min-width: 160px; z-index: 100; opacity: 0; transform: translateY(-10px); transition: all 0.3s ease; }
.dropdown-menu.show { display: block; opacity: 1; transform: translateY(0); }
.dropdown-menu a { display: block; padding: 10px 15px; color: #333; text-decoration: none; }
.dropdown-menu a:hover { background: #f5f0e8; color: #6b4b3e; }

.product-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; padding: 30px 5%; }
.product-card { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 15px; width: 220px; text-align: center; transition: 0.3s; }
.product-card:hover { border: 2px solid #c19a6b; transform: scale(1.03); }
.product-card img { width: 100%; height: 180px; object-fit: contain; border-radius: 8px; }
.product-card h2 { font-size: 1rem; margin: 10px 0 5px; }
.price { color: #6b4b3e; font-weight: bold; margin-bottom: 5px; }
.category, .stock { font-size: 0.85rem; color: #555; margin-bottom: 5px; }
.cart-form { display: flex; flex-direction: column; align-items: center; gap: 5px; }
.quantity-group { display: flex; gap: 5px; align-items: center; }
.qty-input { width: 50px; text-align: center; }
.qty-btn { padding: 3px 8px; background: #6b4b3e; color: white; border: none; cursor: pointer; border-radius: 5px; }
.qty-btn:hover { background: #5a3f33; }
.btn-cart { background: #6b4b3e; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: 0.3s; }
.btn-cart:hover { background: #5a3f33; }
.modal { display: none; position: fixed; z-index: 1000; padding-top: 100px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
.modal-content { background-color: #fff; margin: auto; padding: 20px; border-radius: 10px; width: 250px; text-align: center; position: relative; animation: fadeIn 0.3s ease; }
.close { color: #aaa; position: absolute; top: 10px; right: 15px; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover { color: #000; }
@keyframes fadeIn { from {opacity:0; transform: translateY(-10px);} to {opacity:1; transform: translateY(0);} }
</style>
</head>
<body>
<header>
  <div class="top-header">
    <div class="logo">Fabulous Finds</div>
    <div class="search-bar">
      <span class="material-symbols-outlined">search</span>
      <input type="text" placeholder="Search for items...">
    </div>
    <div class="userlinks">
      <a href="cart.php">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span class="cart-badge"><?php echo $cart_count; ?></span>
      </a>
      <a href="orderlist.php"><span class="material-symbols-outlined">local_shipping</span></a>
      <div class="profile-dropdown">
        <button id="profile-btn"><span class="material-symbols-outlined">account_circle</span></button>
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
    <a href="shop.php">Product</a>
    <a href="contact.php">Contact</a>
  </nav>
</header>

<!-- Modal -->
<div id="cartModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeModal">&times;</span>
    <p id="modalMessage"><?php echo $message; ?></p>
  </div>
</div>

<div class="product-container">
<?php while($row = mysqli_fetch_assoc($result)): 
    $pid = $row['ProductID'];
    $stockLeft = $remainingStock[$pid];
    $stockText = $stockLeft > 0 ? "Stock: $stockLeft" : "❌ Sorry, the item is out of stock";
?>
  <div class="product-card">
    <img src="../assets/img/<?php echo $row['image']; ?>" alt="<?php echo $row['ProductName']; ?>">
    <h2><?php echo $row['ProductName']; ?></h2>
    <p class="category"><?php echo $row['Category']; ?></p>
    <p class="price">₱<?php echo number_format($row['Price'],2); ?></p>
    <p class="stock" id="stock-<?php echo $pid; ?>"><?php echo $stockText; ?></p>

    <form method="post" action="" class="cart-form">
      <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
      <div class="quantity-group">
        <button type="button" class="qty-btn" onclick="decreaseQty(this)">−</button>
        <input type="number" name="quantity" value="1" min="1" class="qty-input" max="<?php echo $stockLeft; ?>" <?php echo $stockLeft == 0 ? 'disabled' : ''; ?>>
        <button type="button" class="qty-btn" onclick="increaseQty(this, <?php echo $stockLeft; ?>)" <?php echo $stockLeft == 0 ? 'disabled' : ''; ?>>+</button>
        <button type="submit" name="add_to_cart" class="btn-cart" <?php echo $stockLeft == 0 ? 'disabled' : ''; ?>>🛒 Add to Cart</button>
      </div>
    </form>
  </div>
<?php endwhile; ?>
</div>

<script>
// Quantity buttons
function decreaseQty(btn) {
  let input = btn.parentElement.querySelector('.qty-input');
  let value = parseInt(input.value);
  if (value > 1) input.value = value - 1;
}
function increaseQty(btn, stock) {
  let input = btn.parentElement.querySelector('.qty-input');
  let value = parseInt(input.value);
  if(value < stock) input.value = value + 1;
}

// Profile dropdown
const profileBtn = document.getElementById("profile-btn");
const dropdownMenu = document.getElementById("dropdown-menu");
profileBtn.addEventListener("click", () => { dropdownMenu.classList.toggle("show"); });
window.addEventListener("click", (e) => {
  if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
    dropdownMenu.classList.remove("show");
  }
});

// Modal & update stock
const modal = document.getElementById("cartModal");
const closeBtn = document.getElementById("closeModal");
const remainingStock = <?php echo json_encode($remainingStock); ?>;

function updateStockDisplay(productID){
    const stockEl = document.getElementById('stock-' + productID);
    const card = stockEl.closest('.product-card');
    const inputEl = card.querySelector('.qty-input');
    const btnEl = card.querySelector('.btn-cart');
    
    if(remainingStock[productID] > 0){
        stockEl.textContent = 'Stock: ' + remainingStock[productID];
        inputEl.disabled = false;
        btnEl.disabled = false;
    } else {
        stockEl.textContent = '❌ Sorry, the item is out of stock';
        inputEl.disabled = true;
        btnEl.disabled = true;
    }
}

<?php if($message && isset($_POST['product_id'])): ?>
modal.style.display = "block";
setTimeout(() => { 
    modal.style.display = "none"; 
    updateStockDisplay(<?php echo $_POST['product_id']; ?>);
}, 2500);
<?php endif; ?>

closeBtn.onclick = () => { 
    modal.style.display = "none"; 
    <?php if(isset($_POST['product_id'])): ?>
        updateStockDisplay(<?php echo $_POST['product_id']; ?>);
    <?php endif; ?>
}
window.onclick = (event) => { if(event.target == modal) modal.style.display = "none"; }
</script>
</body>
</html>
