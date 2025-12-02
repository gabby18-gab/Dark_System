<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fabulous Finds</title>
  <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/client-homepage.css">
</head>
<style>
</style>

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
        </a>

        <a href="orderlist.php">
          <span class="material-symbols-outlined">local_shipping</span>
        </a>

        <!-- 🔸 Profile dropdown -->
        <div class="profile-dropdown">
          <button id="profile-btn">
            <span class="material-symbols-outlined">account_circle</span>
          </button>
          <div class="dropdown-menu" id="dropdown-menu">
            <a href="#">Edit Profile</a>
            <a href="#">Add Address</a>
            <a href="#">Settings</a>
            <a href="../logout.php">Logout</a>
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

  <section class="banner">
    <div class="banner-text">
      <h1>Elevate Your Everyday Style</h1>
      <p>Discover luxury shirts, elegant pants, and timeless perfumes made for you.</p>
      <a href="shop.php" class="btn">Shop Now</a>
    </div>
    <div class="banner-image">
      <img src="../assets/img/model.webp" alt="Model Image">
    </div>
  </section>

  <section class="categories">
    <h3>Featured Categories</h3>
    <div class="category-list">
      <div class="category">
        <img src="../assets/img/lvshirt.jpg" alt="Shirts">
        <p>Shirts</p>
      </div>
      <div class="category">
        <img src="../assets/img/pants.jpg" alt="Pants">
        <p>Pants</p>
      </div>
      <div class="category">
        <img src="../assets/img/lacoste.webp" alt="Perfumes">
        <p>Polo Shirts</p>
      </div>
      <div class="category">
        <img src="../assets/img/lvbag.webp" alt="Perfumes">
        <p>Bags</p>
      </div>
      <div class="category">
        <img src="../assets/img/dior.avif" alt="Perfumes">
        <p>Perfumes</p>
      </div>
    </div>
  </section>
  
  <script src="../assets/js/landing.js"></script>
</body>
</html>
