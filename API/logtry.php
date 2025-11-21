<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <div class="container" id="container">
        <!-- Register Form -->
        <div class="form-container register-container">
            <form action="process_auth.php" method="post">
                <input type="text" placeholder="Name" class="e1" name="name" required>
                <input type="email" placeholder="Email" class="e1" name="email" required>
                <input type="password" placeholder="Password" class="e1" name="password" required>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
        <!-- Login Form -->
        <div class="form-container login-container">
            <form action="process_auth.php" method="post">
                <h1>Login here</h1>
                <?php if(isset($_SESSION['error'])): ?>
                    <p style="color:red"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                <input type="email" placeholder="Email" class="e1" name="email" required>
                <input type="password" placeholder="Password" class="e1" name="password" required>
                <div class="content">
                    <div class="checkbox">
                        <input type="checkbox" name="remember" id="checkbox">
                        <label for="checkbox">Remember me</label>
                    </div>
                    <div class="pass-link">
                        <a href="motp.html">Forgot password ?</a>
                    </div>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
        <!-- Overlay -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1 class="title">Welcome Back!</h1>
                    <p>if you have an account, please login here</p>
                    <button class="ghost" id="loginBtn">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1 class="title">Hello, Friend!</h1>
                    <p>if you don't have an account yet, register here</p>
                    <button class="ghost" id="registerBtn">Register</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const container = document.getElementById('container');
        registerBtn.addEventListener('click', () => container.classList.add('right-panel-active'));
        loginBtn.addEventListener('click', () => container.classList.remove('right-panel-active'));
    </script>
</body>
</html>
