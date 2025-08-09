<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - ShadowStrike</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Verify Your Email</h1>
        <form action="verify_process.php" method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="text" name="code" placeholder="Enter the verification code" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>

