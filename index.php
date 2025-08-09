<?php
session_start();

// Secure headers to mitigate attacks
//header('X-Frame-Options: DENY');           // Prevent clickjacking
//header("Content-Security-Policy: default-src 'self'; style-src 'self'; script-src 'self'"); // Restrict content sources
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offensive Security - Penetration Testing</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            font-family: 'Orbitron', monospace;
            background-image: url('Images/9.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
        }
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: rgba(39, 39, 39, 0.4);
        }
        /* Navigation */
        .nav {
            position: fixed;
            top: 0;
            display: flex;
            justify-content: space-around;
            width: 100%;
            height: 80px;
            line-height: 80px;
            background: linear-gradient(rgba(39, 39, 39, 0.8), transparent);
            z-index: 100;
        }
        .nav-logo p {
            color: white;
            font-size: 25px;
            font-weight: 600;
        }
        .nav-menu {
            display: grid;
            grid: auto-flow / repeat(3, 160px);
            justify-content: space-evenly;
            width: 74%;
        }
        .nav-menu ul {
            display: flex;
            padding: 0;
            margin: 0;
        }
        .nav-menu ul li {
            list-style-type: none;
        }
        .nav-menu ul li .link {
            text-decoration: none;
            font-weight: 500;
            color: #fff;
            padding-bottom: 10px;
            margin: 0 40px;
        }
        .link:hover, .active {
            border-bottom: 2px solid #fff;
        }
        .nav-button .btn {
            width: 130px;
            height: 40px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.4);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: .3s ease;
        }
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .btn.white-btn {
            background: rgba(255, 255, 255, 0.7);
        }
        .nav-menu-btn {
            display: none;
        }

/* Enhanced Container Style */
.container {
    position: relative;
    display: flex;
    flex-direction: column; /* Ensures vertical alignment of elements inside */
    align-items: center;
    justify-content: center;
    width: 100%;
    max-width: 500px; /* Ensures responsiveness on smaller screens */
    height: auto; /* Adjust height based on content */
    padding: 20px;
    margin: 20px auto; /* Center horizontally with a margin */
    background: rgba(0, 0, 0, 0.8); /* Slightly darker for contrast */
    border-radius: 16px; /* Slightly larger border radius for smoother edges */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5); /* More prominent shadow */
    z-index: 2;
    overflow: hidden;
    transition: transform 0.3s ease, opacity 0.3s ease; /* For smooth animations */
}

/* Adjust text header in the container */
.container header {
    color: #ffffff;
    font-size: 30px;
    font-weight: bold;
    text-shadow: 0 0 8px #ff4d4d, 0 0 16px #ff6666;
    margin-bottom: 20px;
    text-align: center;
}

/* Responsive Design for Smaller Screens */
@media only screen and (max-width: 768px) {
    .container {
        max-width: 90%;
        padding: 15px;
        margin: 10px auto;
    }
    .container header {
        font-size: 22px;
    }
}

        header {
            color: #fff;
            font-size: 30px;
            text-align: center;
            padding: 10px 0 30px 0;
        }
        .input-box i {
            position: relative;
            top: -35px;
            left: 17px;
            color: #ff3333;
        }
        .input-box iu {
            position: relative;
            top: -38px;
            left: 355px;
            color: #ff3333;
        }
        .toggle-btn {
            margin-left: 10px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }
        .input-field {
            font-size: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            height: 50px;
            width: 100%;
            padding: 0 10px 0 45px;
            border: none;
            border-radius: 30px;
            outline: none;
            transition: .2s ease;
        }
        .input-field:hover, .input-field:focus {
            background: rgba(255, 255, 255, 0.25);
        }
        ::-webkit-input-placeholder {
            color: #fff;
        }
        .submit {
            font-size: 15px;
            font-weight: 500;
            color: black;
            height: 45px;
            width: 100%;
            border: none;
            border-radius: 30px;
            outline: none;
            background: #b3b3b3;
            cursor: pointer;
            transition: .3s ease-in-out;
        }
        .submit:hover {
            background: rgba(255, 255, 255, 0.5);
            box-shadow: 1px 5px 7px 1px rgba(0, 0, 0, 0.2);
        }
        .register-text {
            color: white;
            font-size: 16px;
            text-align: center;
            margin-top: 15px;
        }
        .register-link {
            color: #ff4d4d;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s, text-shadow 0.3s;
        }
        .register-link:hover {
            color: #ff1a1a;
            text-shadow: 0 0 10px #ff3333;
        }
        @media only screen and (max-width: 786px) {
            .nav-button {
                display: none;
            }
            .nav-menu {
                top: -800px;
                background: rgba(255, 255, 255, 0.2);
                height: 90vh;
                transition: .3s;
            }
            .nav-menu ul {
                flex-direction: column;
                text-align: center;
            }
            .nav-menu-btn {
                display: block;
            }
            .nav-menu-btn i {
                font-size: 25px;
                color: #fff;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
            }
        }
        p {
            color: #fff;
        }
        .glow {
            animation: glow 1.5s infinite alternate;
        }
        @keyframes glow {
            from {
                text-shadow: 0 0 5px #ff1a1a, 0 0 10px #ff4d4d;
            }
            to {
                text-shadow: 0 0 20px #ff0000, 0 0 40px #ff3333;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Navigation -->
        <nav class="nav">
            <div class="nav-logo">
                <p class="glow">ShadowStrike Hub</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link active">Home</a></li>
                    <li><a href="services.php" class="link">Overview</a></li>
                    <li><a href="about.php" class="link">About</a></li>
                </ul>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <!-- Form Container -->
        <div class="container">
            <header>WELCOME TO <br>SHADOWSTRIKE TOOL</header>
            <form action="login_process.php" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="input-box">
                    <input type="email" class="input-field" name="email" placeholder="Email" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" id="password" name="password" placeholder="Password" required>
                    <i class="bx bx-lock-alt"></i>
                    <iu class="bx bx-lock-alti"><button type="button" class="toggle-btn" onclick="togglePassword()"></button></iu>
                </div>
                <div class="input-box">
                    <input type="submit" value="LOG IN" name="login" class="submit">
                    <p class="register-text">New user? <a href="register.php" class="register-link">Create an account</a></p>
                </div>
            </form>

            </div>
        </div>
    </div>
</body>
</html>

