<?php
session_start();
require_once "includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) { 
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php"); 
        exit();
    } else { 
        $error = "Invalid username or password"
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Cybersecurity Audit Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #fff;
            border-raidus: 10px;
            padding: 30px;
            width: 350px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #2c3e50; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input { 
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc; 
            margin-top: 5px;
        }
        button { 
            width: 100%;
            background: #0078D4;
            color: white; 
            border: none;
            padding: 10px;
            margin-top: 15px;
            border-radius:6px; 
            cursor: pointer;
        }
        button:hover { background #005fa3}
        .error { 
            color: red; 
            text-align: center;
            margin-bottom: 10px;
        }
        .footer-link { 
            text-align: center;
            margin-top: 15px;
        }
        .footer-link a { 
            color: #0078d4
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Cybersecurity Audit Tracker</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div class="footer-link">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>