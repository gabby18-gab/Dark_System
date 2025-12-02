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

// Sales report data - EXCLUDE CANCELLED ORDERS
$sales_report_query = "
    SELECT 
        DATE(o.OrderDate) as order_date,
        COUNT(*) as order_count,
        SUM(py.Amount) as daily_revenue,
        AVG(py.Amount) as avg_order_value
    FROM orders o
    LEFT JOIN payment py ON o.OrderID = py.OrderID
    WHERE o.OrderDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    AND o.Status != 'Cancelled'
    AND o.Status != 'Pending'
    GROUP BY DATE(o.OrderDate)
    ORDER BY order_date DESC
";
$sales_report_result = $conn->query($sales_report_query);

// Inventory report (this one doesn't need changes as it's about stock levels)
$inventory_report_query = "
    SELECT 
        p.ProductName,
        p.Category,
        p.Price,
        p.StockQuantity,
        s.Name as SellerName,
        COUNT(od.ProductID) as times_ordered
    FROM product p
    LEFT JOIN seller s ON p.SellerID = s.SellerID
    LEFT JOIN orderdetails od ON p.ProductID = od.ProductID
    GROUP BY p.ProductID
    ORDER BY p.StockQuantity ASC
";
$inventory_report_result = $conn->query($inventory_report_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet" />
  <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png" />
  <link rel="stylesheet" href="../assets/css/admin-style.css" />
  <title>Reports - Fabulous Finds</title>
</head>

<body">
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
        <a href="order_summary.php">
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
        <a href="reports.php" class="active">
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
      <h1>Sales & Inventory Reports</h1>

      <div class="recent-orders">
        <h2>Sales Report (Last 7 Days)</h2>
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Orders</th>
              <th>Revenue</th>
              <th>Avg Order Value</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($report = $sales_report_result->fetch_assoc()): ?>
              <tr>
                <td><?php echo date('M j, Y', strtotime($report['order_date'])); ?></td>
                <td><?php echo $report['order_count']; ?></td>
                <td>₱<?php echo number_format($report['daily_revenue'] ?? 0, 2); ?></td>
                <td>₱<?php echo number_format($report['avg_order_value'] ?? 0, 2); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div class="recent-orders" style="margin-top: 2rem;">
        <h2>Inventory Report</h2>
        <table>
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Times Ordered</th>
              <th>Seller</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($inventory = $inventory_report_result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $inventory['ProductName']; ?></td>
                <td><?php echo $inventory['Category']; ?></td>
                <td>₱<?php echo number_format($inventory['Price'], 2); ?></td>
                <td class="<?php echo $inventory['StockQuantity'] < 10 ? 'danger' : 'success'; ?>">
                  <?php echo $inventory['StockQuantity']; ?>
                </td>
                <td><?php echo $inventory['times_ordered']; ?></td>
                <td><?php echo $inventory['SellerName']; ?></td>
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