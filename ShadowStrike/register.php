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
            width: 70%;
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
        /* Registration Form */
        .form-box {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 500px;
            height: 460px;
            overflow: hidden;
            z-index: 2;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }
        .form-container {
            width: 100%;
            max-width: 495px;
            display: flex;
            flex-direction: column;
            transition: .5s ease-in-out;
            opacity: 1;
            padding: 20px;
        }
        header {
            color: #fff;
            font-size: 30px;
            text-align: center;
            padding: 10px 0 30px 0;
        }
        .input-box {
            position: relative;
            margin-bottom: 20px;
        }
        .input-box input {
            font-size: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            height: 50px;
            width: 100%;
            padding: 0 10px;
            border: none;
            border-radius: 30px;
            outline: none;
            transition: .2s ease;
        }
        .input-box input:focus {
            background: rgba(255, 255, 255, 0.25);
        }
        .input-box input::placeholder {
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
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Navigation -->
        <nav class="nav">
            <div class="nav-logo">
                <p>Offensive Hub</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link">Home</a></li>
                    <li><a href="services.php" class="link">Overview</a></li>
                    <li><a href="about.php" class="link">About</a></li>
                </ul>
            </div>
        </nav>

        <!-- Registration Form -->
        <div class="form-box">
            <div class="form-container">
                <header>Create Account</header>
                <form action="register_process.php" method="post">
                    <div class="input-box">
                        <input type="text" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="input-box">
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="telegram_bot" placeholder="Telegram bot (if available)">
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="input-box">
                        <input type="submit" value="Register" class="submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

