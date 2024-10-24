<style>
    /* General layout for full-page flex */
    body, html {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

   

    header .navbar-brand {
        color: #ffc107; /* Bright yellow for brand name */
        font-weight: bold;
        font-size: 1.7rem;
        letter-spacing: 0.05rem; /* Add some spacing for a polished look */
    }

    header .navbar-nav .nav-link {
        color: white;
        margin-right: 15px;
        font-size: 1.1rem; /* Slightly larger font size for readability */
        transition: color 0.3s ease;
    }

    header .navbar-nav .nav-link:hover {
        color: #ffc107; /* Consistent hover color with brand and cards */
    }

    /* Main content styling */
    main {
        flex-grow: 1; /* This makes the main section fill available space */
        margin-top: 20px;
    }

    /* Cards styling */
    .card {
        width: 100%;
        max-width: 18rem; /* Limit the card width for a neat look */
        margin-bottom: 30px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Add a soft shadow for card depth */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth animation */
    }

    .card:hover {
        transform: translateY(-10px); /* Lift effect on hover */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); /* Slightly stronger shadow on hover */
    }

    .card img {
        border-radius: 5px 5px 0 0; /* Round the top corners of the image */
        height: 150px;
        object-fit: cover; /* Ensure the image fits well */
    }

    .card-title {
        color: #343a40; /* Strong dark color for card titles */
        font-size: 1.3rem;
        font-weight: bold;
    }

    .card-text {
        color: #6c757d; /* Muted color for card text */
    }

    /* Footer Styling */
    footer {
        background-color: #343a40;
        color: white;
        padding: 15px 0;
        text-align: center;
        font-size: 0.9rem;
        border-top: 3px solid #ffc107; /* Consistent yellow accent */
        position: absolute;
        bottom: 0;
        width: 100%; /* Ensure the footer takes full width */
    }

    footer p {
        margin: 0;
    }

    /* Media queries for responsiveness */
    @media (min-width: 768px) {
        .card-deck {
            display: flex;
            justify-content: space-around; /* Spread out cards evenly */
        }
    }

    @media (max-width: 767px) {
        .card {
            margin-bottom: 20px; /* Add spacing between cards on small screens */
        }
    }
</style>


</head>
<body>

<?php session_start(); ?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">RevRides Rental</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="home.php">Home</a>
                    <a class="nav-link" href="user/profile.php">Profile</a>
                    <?php
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                        echo '<a class="nav-link" href="logout.php">Logout</a>';
                    } else {
                        echo '<a class="nav-link" href="login.php">Login</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>
</header>


    <?php 
            // Other included functionality here

          // Logout functionality
           if (isset($_GET['logout'])) {
           session_start();
           session_unset();
           session_destroy();
           echo "<script>alert('Logout successful');</script>";
           header("Location: home.php");
           exit();
           }
    ?>

   <!-- <footer>
        <p>© 2024 Bike Rental System. All rights reserved.</p>
    </footer>
        -->