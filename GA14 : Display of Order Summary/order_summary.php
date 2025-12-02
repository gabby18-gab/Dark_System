<?php
session_start();

// Direct database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "fabulous_finds";

$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// Fetch order summary - EXCLUDE CANCELLED ORDERS
$summary_query = "
    SELECT 
        COUNT(*) as total_orders,
        SUM(py.Amount) as total_revenue,
        AVG(py.Amount) as avg_order_value,
        COUNT(DISTINCT o.UserID) as unique_customers
    FROM orders o
    LEFT JOIN payment py ON o.OrderID = py.OrderID
    WHERE o.OrderDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    AND o.Status != 'Cancelled'
    AND o.Status != 'Pending'
";
$summary_result = $conn->query($summary_query);
$summary = $summary_result->fetch_assoc();

// Top selling products - EXCLUDE CANCELLED ORDERS
$top_products_query = "
    SELECT p.ProductName, SUM(od.Quantity) as total_sold
    FROM orderdetails od
    JOIN product p ON od.ProductID = p.ProductID
    JOIN orders o ON od.OrderID = o.OrderID
    WHERE o.OrderDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    AND o.Status != 'Cancelled'
    AND o.Status != 'Pending'
    GROUP BY p.ProductID
    ORDER BY total_sold DESC
    LIMIT 5
";
$top_products_result = $conn->query($top_products_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet" />
  <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png" />
  <link rel="stylesheet" href="../assets/css/admin-style.css" />
  <title>Order Summary - Fabulous Finds</title>
</head>

<body>
  <div class="container">
    <aside>
      <div class="top">
        <div class="logo">
          <img src="../assets/img/Fabulous-finds.png" alt="Logo" class="site-logo" />
          <h2>FABULOUS <span class="primary">FINDS</span></h2>
        </div>
        <div class="close" id="close-btn">
          <span class="material-icons-sharp">close</span>
        </div>
      </div>
      <div class="sidebar">
        <a href="index.php">
          <span class="material-icons-sharp">grid_view</span>
          <h3>Dashboard</h3>
        </a>
        <a href="products.php">
          <span class="material-icons-sharp">inventory</span>
          <h3>Products</h3>
        </a>
        <a href="orders.php">
          <span class="material-icons-sharp">receipt_long</span>
          <h3>Orders</h3>
        </a>
        <a href="order_summary.php" class="active">
          <span class="material-icons-sharp">summarize</span>
          <h3>Order Summary</h3>
        </a>
        <a href="order_history.php">
          <span class="material-icons-sharp">history</span>
          <h3>Order History</h3>
        </a>
        <a href="invoice.php">
          <span class="material-icons-sharp">receipt</span>
          <h3>Invoice/Receipt</h3>
        </a>
        <a href="reports.php">
          <span class="material-icons-sharp">assessment</span>
          <h3>Reports</h3>
        </a>
        <a href="add_product.php">
          <span class="material-icons-sharp">add</span>
          <h3>Add Product</h3>
        </a>
        <a href="logout.php">
          <span class="material-icons-sharp">logout</span>
          <h3>Logout</h3>
        </a>
      </div>
    </aside>
    <main>
      <h1>Order Summary</h1>
      <div class="insights">
        <div class="sales">
          <span class="material-icons-sharp">analytics</span>
          <div class="middle">
            <div class="left">
              <h3>Total Orders (30 days)</h3>
              <h1><?php echo $summary['total_orders']; ?></h1>
            </div>
          </div>
        </div>
        <div class="expenses">
          <span class="material-icons-sharp">bar_chart</span>
          <div class="middle">
            <div class="left">
              <h3>Total Revenue</h3>
              <h1>₱<?php echo number_format($summary['total_revenue'] ?? 0, 2); ?></h1>
            </div>
          </div>
        </div>
        <div class="income">
          <span class="material-icons-sharp">stacked_line_chart</span>
          <div class="middle">
            <div class="left">
              <h3>Avg Order Value</h3>
              <h1>₱<?php echo number_format($summary['avg_order_value'] ?? 0, 2); ?></h1>
            </div>
          </div>
        </div>
      </div>
      <div class="recent-orders">
        <h2>Top Selling Products (Last 30 Days)</h2>
        <table>
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Total Sold</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($product = $top_products_result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $product['ProductName']; ?></td>
                <td><?php echo $product['total_sold']; ?> units</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </main>
    <div class="right">
      <div class="top">
        <button id="menu-btn">
          <span class="primary material-icons-sharp">menu</span>
        </button>
        <div class="theme-toggler">
          <span class="material-icons-sharp active">light_mode</span>
          <span class="material-icons-sharp">dark_mode</span>
        </div>
        <div class="profile">
          <div class="info">
            <p>Hey, <b>Admin</b></p>
            <small class="text-muted">Administrator</small>
          </div>
          <div class="profile-photo">
            <img src="../assets/img/profile.jpg" />
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="../assets/js/admin-js.js"></script>
</body>

</html>
<?php $conn->close(); ?>