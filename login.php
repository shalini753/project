<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | R&D Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #000;
            overflow: hidden;
        }
        
        section {
            position: absolute;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2px;
            flex-wrap: wrap;
            overflow: hidden;
        }
        
        section::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(#000, #4e54c8, #000);
            animation: animate 5s linear infinite;
        }
        
        @keyframes animate {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100%);
            }
        }
        
        section span {
            position: relative;
            display: block;
            width: calc(6.25vw - 2px);
            height: calc(6.25vw - 2px);
            background: #181818;
            z-index: 2;
            transition: 1.5s;
        }
        
        section span:hover {
            background: #4e54c8;
            transition: 0s;
        }
        
        section .signin {
            position: absolute;
            width: 400px;
            background: #222;
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }
        
        section .signin .content {
            position: relative;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 40px;
        }
        
        section .signin .content h2 {
            font-size: 2em;
            color: #4e54c8;
            text-transform: uppercase;
        }
        
        section .signin .content .form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        section .signin .content .form .inputBox {
            position: relative;
            width: 100%;
        }
        
        section .signin .content .form .inputBox input {
            position: relative;
            width: 100%;
            background: #333;
            border: none;
            outline: none;
            padding: 25px 10px 7.5px;
            border-radius: 4px;
            color: #fff;
            font-weight: 500;
            font-size: 1em;
        }
        
        section .signin .content .form .inputBox i {
            position: absolute;
            left: 0;
            padding: 15px 10px;
            font-style: normal;
            color: #aaa;
            transition: 0.5s;
            pointer-events: none;
        }
        
        .signin .content .form .inputBox input:focus ~ i,
        .signin .content .form .inputBox input:valid ~ i {
            transform: translateY(-7.5px);
            font-size: 0.8em;
            color: #fff;
        }
        
        .signin .content .form .links {
            position: relative;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        
        .signin .content .form .links a {
            color: #fff;
            text-decoration: none;
        }
        
        .signin .content .form .links a:nth-child(2) {
            color: #4e54c8;
            font-weight: 600;
        }
        
        .signin .content .form .inputBox input[type="submit"] {
            padding: 10px;
            background: #4e54c8;
            color: #fff;
            font-weight: 600;
            font-size: 1.35em;
            letter-spacing: 0.05em;
            cursor: pointer;
        }
        
        input[type="submit"]:active {
            opacity: 0.6;
        }
        
        @media (max-width: 900px) {
            section span {
                width: calc(10vw - 2px);
                height: calc(10vw - 2px);
            }
        }
        
        @media (max-width: 600px) {
            section span {
                width: calc(20vw - 2px);
                height: calc(20vw - 2px);
            }
        }
        
        .error-message {
            background-color: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <section>
        <?php for($i = 0; $i < 100; $i++): ?>
            <span></span>
        <?php endfor; ?>

        <div class="signin">
            <div class="content">
                <h2>R&D Dashboard Login</h2>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
                    </div>
                <?php endif; ?>
                
                <form class="form" action="auth.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="inputBox">
                        <input type="text" name="username" required>
                        <i>Username</i>
                    </div>
                    
                    <div class="inputBox">
                        <input type="password" name="password" required>
                        <i>Password</i>
                    </div>
                    
                    <div class="links">
                        <a href="#">&nbsp;</a>
                        <a href="signup.php">Sign Up</a>
                    </div>
                    
                    <div class="inputBox">
                        <input type="submit" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <script>
        // Create animated background
        const section = document.querySelector('section');
        const spans = document.querySelectorAll('span');
        
        spans.forEach(span => {
            span.style.animationDelay = Math.random() * 5 + 's';
            span.style.animationDuration = Math.random() * 5 + 5 + 's';
        });
    </script>
</body>
</html>