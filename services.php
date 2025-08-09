<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offensive Security - Our Services</title>
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
            background-image: url('Images/9.jpg'); /* Change this to your image path */
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
            background: rgba(39, 39, 39, 0.4); /* Dark overlay */
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
	    display: flex;
	    display: grid;
	    grid: auto-flow / repeat(3, 80px);
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
        /* Content */
        .content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }
        header {
            color: #fff;
            font-size: 30px;
            text-align: center;
            padding: 20px 0;
        }
        h2 {
            margin: 15px 0;
            color: #b3b3b3;
        }
        p {
            color: #fff;
          /*  font-weight: bold;*/
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
        .nav-menu-btn {
            display: none;
        }
        /* Mobile */
        @media only screen and (max-width: 786px) {
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
                    <li><a href="index.php" class="link">Home</a></li>
                    <li><a href="services.php" class="link active">Overview</a></li>
                    <li><a href="about.php" class="link">About</a></li>
                </ul>
            </div>
            <div class="nav-button">
                <!-- Buttons can be added here if needed -->
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <div class="content">
           <!-- <header>Our Services</header>-->
            <h2>ShadowStrike Tools</h2>
            <p>Offensive security tools are specialized software applications and frameworks designed to help security professionals identify and exploit vulnerabilities within systems, networks, and applications. These tools simulate real-world attack scenarios, allowing ethical hackers to assess the security posture of an organization. By leveraging a variety of techniques—from network scanning and vulnerability assessment to penetration testing and exploitation—offensive security tools provide valuable insights into potential weaknesses that could be exploited by malicious actors. Common tools like Metasploit, Nmap, and Burp Suite offer a comprehensive suite of functionalities, enabling security teams to uncover risks, validate security measures, and strengthen defenses.<br><br> Ultimately, the use of offensive security tools is essential for organizations aiming to proactively defend against cyber threats and enhance their overall cybersecurity strategies.</p>
  <h2></h2>
        </div>
    </div>

    <script>
        function myMenuFunction() {
            var navMenu = document.getElementById("navMenu");
            if (navMenu.style.top === "-800px") {
                navMenu.style.top = "80px";
            } else {
                navMenu.style.top = "-800px";
            }
        }
    </script>
</body>
</html>
