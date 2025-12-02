<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabulous Finds – Contact</title>
    <link rel="icon" type="image/png" href="../assets/img/Fabulous-finds.png">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <style>
        /* =========================
           RESET & BASE
        ========================= */
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: Arial, sans-serif;
        }

        body {
          background: #fff9e9;
          color: #333;
        }

        /* =========================
           HEADER
        ========================= */
        header {
          background: #fff;
          border-bottom: 1px solid #ddd;
          padding: 15px 5%;
        }

        /* --- TOP HEADER: logo + search bar katabi, user icons sa kanan --- */
        .top-header {
          display: flex;
          align-items: center;
          gap: 20px; /* space between logo and search bar */
        }

        .logo {
          font-weight: bold;
          font-size: 1.5rem;
          color: #333;
          flex-shrink: 0; /* prevent logo from shrinking */
        }

        /* Search bar (katabi ng logo) */
        .search-bar {
          position: relative;
          flex: 1;          /* kunin ang natitirang space sa row */
          min-width: 150px;
          max-width: 550px; /* adjust width kung gaano kahaba */
        }

        .search-bar input {
          width: 100%;
          padding: 8px 12px 8px 35px;
          border: 1px solid #ccc;
          border-radius: 20px;
          box-sizing: border-box;
        }

        .search-bar .material-symbols-outlined {
          position: absolute;
          top: 50%;
          left: 10px;
          transform: translateY(-50%);
          font-size: 20px;
          color: #888;
        }

        /* User links (cart, account, delivery truck) - sa kanan */
        .userlinks {
          display: flex;
          align-items: center;
          gap: 15px;
          margin-left: auto; /* push icons sa kanan */
        }

        .userlinks a {
          text-decoration: none;
          color: #555;
          font-size: 0.95rem;
          display: inline-flex;
          align-items: center;
        }

        .userlinks a:hover {
          color: #c19a6b;
        }

        /* Icon styling */
        .material-symbols-outlined {
          font-size: 22px;
          vertical-align: middle;
        }

        /* --- NAV MENU --- */
        .menu {
          text-align: left; /* move links to the left */
          margin-top: 12px;
          border-top: 1px solid #eee;
          padding-top: 10px;
        }

        .menu a {
          text-decoration: none;
          color: #555;
          margin-right: 20px; /* use margin-right instead of margin 0 20px for left alignment */
          font-size: 0.95rem;
          font-weight: 500;
        }

        .menu a:hover,
        .menu a.active {
          color: #c19a6b;
        }

        /* Profile dropdown */
        .profile-dropdown {
          position: relative;
        }

        .profile-dropdown button {
          background: none;
          border: none;
          cursor: pointer;
          color: #555;
          padding: 5px;
        }

        .profile-dropdown button:hover {
          color: #c19a6b;
        }

        /* Dropdown menu design */
        .dropdown-menu {
          display: none;
          position: absolute;
          right: 0;
          top: 35px;
          background: #fff;
          border: 1px solid #ddd;
          border-radius: 8px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.1);
          min-width: 160px;
          z-index: 100;
          opacity: 0;
          transform: translateY(-10px);
          transition: all 0.3s ease;
        }

        .dropdown-menu a {
          display: block;
          padding: 10px 15px;
          color: #333;
          text-decoration: none;
          font-size: 0.95rem;
          border-radius: 8px;
        }

        .dropdown-menu a:hover {
          background: #f5f0e8;
          color: #6b4b3e;
        }

        /* Show dropdown (with fade effect) */
        .dropdown-menu.show {
          display: block;
          opacity: 1;
          transform: translateY(0);
        }

        /* =========================
           CONTACT PAGE LAYOUT
        ========================= */
        .contact-wrapper {
          width: 100%;
          display: flex;
          justify-content: center;
          margin-top: 60px;
          margin-bottom: 80px;
          padding: 0 20px; /* Add horizontal space */
        }

        .big-card {
          display: flex;
          gap: 40px;
          background: #fff;
          padding: 40px;
          border-radius: 20px;
          width: 100%;
          max-width: 1000px; /* slightly smaller for spacing */
          box-shadow: 0px 0px 20px rgba(0,0,0,0.12);
        }

        /* LEFT SIDE */
        .contact-info {
          background: #f5f0e8;
          padding: 25px;
          border-radius: 15px;
          flex: 1;
        }

        .contact-info h2 {
          font-size: 1.8rem;
          color: #6b4b3e;
          margin-bottom: 10px;
        }

        .contact-info p {
          margin-bottom: 8px;
          font-size: 1rem;
        }

        /* RIGHT SIDE */
        .contact-form {
          background: #ffffff;
          padding: 25px;
          border-radius: 15px;
          flex: 1.1; /* slightly wider than left side */
        }

        .form-group {
          margin-bottom: 18px;
        }

        label {
          font-weight: bold;
          margin-bottom: 6px;
          display: block;
        }

        /* INPUTS */
        input,
        textarea {
          width: 100%;
          padding: 12px;
          border-radius: 8px;
          border: 1px solid #ccc;
          font-size: 1rem;
        }

        /* FILE UPLOAD */
        .file-container {
          display: flex;
          align-items: center;
          gap: 10px;
        }

        #file {
          display: none;
        }

        .custom-file-btn {
          background: #ddd;
          padding: 7px 12px;
          border-radius: 6px;
          cursor: pointer;
        }

        #fileLabel {
          font-size: 0.9rem;
          color: #555;
        }

        /* Hidden class for remove file button */
        .hidden {
          display: none;
        }

        /* SUBMIT BUTTON */
        .submit-btn {
          background: #6b4b3e;
          color: #fff;
          border: none;
          padding: 12px 18px;
          border-radius: 6px;
          cursor: pointer;
          font-weight: bold;
          width: 100%;
          margin-top: 10px;
          transition: 0.3s;
        }

        .submit-btn:hover {
          background: #5a3f33;
        }

        /* =========================
           RESPONSIVE ADJUSTMENTS
        ========================= */
        @media (max-width: 768px) {
          .big-card {
            flex-direction: column;
            gap: 30px;
            padding: 30px;
          }

          .contact-wrapper {
            padding: 0 15px;
          }
        }
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
            <a href="logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <nav class="menu">
      <a href="index.php">Home</a>
      <a href="shop.php">Product</a>
      <a href="contact.php" class="active">Contact</a>
    </nav>
</header>

<section class="contact-wrapper">

    <div class="big-card">

        <!-- LEFT CARD -->
        <div class="contact-info">
            <h2>Customer Service</h2>
            <p>If you have any questions, feel free to contact us anytime.</p>

            <p><strong>Email:</strong> support@fabulousfinds.com</p>
            <p><strong>Phone:</strong> +63 912 345 6789</p>
        </div>

        <!-- RIGHT CARD -->
        <form id="contactForm" class="contact-form" action="send_message.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" placeholder="Type your message here..." required></textarea>
            </div>

            <div class="form-group file-upload">
                <label for="file">Attach an image (optional)</label>
                <div class="file-container">
                    <input type="file" id="file" name="file" accept="image/*">
                    <label for="file" class="custom-file-btn">Choose File</label>
                    <span id="fileLabel">No file chosen</span>
                    <button type="button" id="removeFile" class="hidden">✖</button>
                </div>
            </div>

            <button type="submit" class="submit-btn">Send Message</button>

        </form>

    </div>

</section>

<script>
    // Profile dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('profile-btn');
        const dropdownMenu = document.getElementById('dropdown-menu');
        
        if (profileBtn && dropdownMenu) {
            profileBtn.addEventListener('click', function() {
                dropdownMenu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!profileBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
        
        // File upload functionality
        const fileInput = document.getElementById('file');
        const fileLabel = document.getElementById('fileLabel');
        const removeFileBtn = document.getElementById('removeFile');
        
        if (fileInput && fileLabel && removeFileBtn) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileLabel.textContent = this.files[0].name;
                    removeFileBtn.classList.remove('hidden');
                } else {
                    fileLabel.textContent = 'No file chosen';
                    removeFileBtn.classList.add('hidden');
                }
            });
            
            removeFileBtn.addEventListener('click', function() {
                fileInput.value = '';
                fileLabel.textContent = 'No file chosen';
                removeFileBtn.classList.add('hidden');
            });
        }
    });
</script>

</body>
</html>