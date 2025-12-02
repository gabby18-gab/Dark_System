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

// Fetch orders for invoice generation
$invoices_query = "
    SELECT o.OrderID, u.Name as CustomerName, u.Email, u.Address,
           s.Name as SellerName, s.ContactInfo as SellerContact,
           p.ProductName, p.Price, od.Quantity,
           py.Amount, py.PaymentMethod, py.PaymentDate,
           o.OrderDate, o.Status
    FROM orders o
    JOIN user u ON o.UserID = u.UserID
    JOIN seller s ON o.SellerID = s.SellerID
    JOIN orderdetails od ON o.OrderID = od.OrderID
    JOIN product p ON od.ProductID = p.ProductID
    LEFT JOIN payment py ON o.OrderID = py.OrderID
    ORDER BY o.OrderDate DESC
    LIMIT 10
";
$invoices_result = $conn->query($invoices_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet" />
  <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png" />
  <link rel="stylesheet" href="../assets/css/admin-style.css" />
  <title>Invoices - Fabulous Finds</title>
  <style>
    .invoice-container {
      background: var(--color-white);
      padding: var(--card-padding);
      border-radius: var(--card-border-radius);
      box-shadow: var(--box-shadow);
      margin-top: 1rem;
    }

    .invoice-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2rem;
      border-bottom: 2px solid var(--color-light);
      padding-bottom: 1rem;
    }

    .invoice-details {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .invoice-items table {
      width: 100%;
      border-collapse: collapse;
    }

    .invoice-items th,
    .invoice-items td {
      padding: 0.8rem;
      border-bottom: 1px solid var(--color-light);
      text-align: left;
    }

    .invoice-total {
      text-align: right;
      margin-top: 1rem;
      font-size: 1.2rem;
      font-weight: bold;
    }

    .print-btn {
      margin-top: 1rem;
      background: var(--color-primary);
      color: var(--color-white);
      padding: 0.8rem 1.5rem;
      border-radius: var(--border-radius-1);
      cursor: pointer;
      border: none;
    }
  </style>
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
        <a href="invoice.php" class="active">
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
      <h1>Invoice & Receipt Management</h1>

      <?php while ($invoice = $invoices_result->fetch_assoc()): ?>
        <div class="invoice-container">
          <div class="invoice-header">
            <div>
              <h2>FABULOUS FINDS</h2>
              <p>Polangui<br>Albay, 4505<br>Phone: (555) 123-4567</p>
            </div>
            <div style="text-align: right;">
              <h2>INVOICE</h2>
              <p>Invoice #: FF<?php echo str_pad($invoice['OrderID'], 6, '0', STR_PAD_LEFT); ?></p>
              <p>Date: <?php echo date('M j, Y', strtotime($invoice['OrderDate'])); ?></p>
            </div>
          </div>

          <div class="invoice-details">
            <div>
              <h3>Bill To:</h3>
              <p><strong><?php echo $invoice['CustomerName']; ?></strong><br>
                <?php echo $invoice['Email']; ?><br>
                <?php echo $invoice['Address']; ?></p>
            </div>
            <div>
              <h3>From:</h3>
              <p><strong><?php echo $invoice['SellerName']; ?></strong><br>
                <?php echo $invoice['SellerContact']; ?></p>
            </div>
          </div>

          <div class="invoice-items">
            <table>
              <thead>
                <tr>
                  <th>Description</th>
                  <th>Quantity</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?php echo $invoice['ProductName']; ?></td>
                  <td><?php echo $invoice['Quantity']; ?></td>
                  <td>₱<?php echo number_format($invoice['Price'], 2); ?></td>
                  <td>₱<?php echo number_format($invoice['Amount'] ?? 0, 2); ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="invoice-total">
            <p>Total: ₱<?php echo number_format($invoice['Amount'] ?? 0, 2); ?></p>
            <p>Payment Method: <?php 
              if (isset($invoice['PaymentMethod'])) {
                if ($invoice['PaymentMethod'] == 'gcash') {
                  echo 'GCash';
                } elseif ($invoice['PaymentMethod'] == 'paymaya') {
                  echo 'PayMaya';
                } elseif ($invoice['PaymentMethod'] == 'cod') {
                  echo 'Cash on Delivery';
                } else {
                  echo $invoice['PaymentMethod'];
                }
              } else {
                echo 'N/A';
              }
            ?></p>
            <p>Status: <span class="<?php echo $invoice['Status'] == 'Completed' ? 'success' : ($invoice['Status'] == 'Cancelled' ? 'danger' : 'warning'); ?>">
                <?php echo $invoice['Status']; ?>
              </span></p>
          </div>

          <button class="print-btn" onclick="window.print()">Print Invoice</button>
        </div>
      <?php endwhile; ?>
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