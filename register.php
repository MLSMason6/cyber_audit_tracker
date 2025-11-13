<?php
session_start();
require_once "includes/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) { 
        $message = "⚠️ Passwords do not match.";
    } else { 
        // Check if username already exists 
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) { 
            $message = "⚠️ Username already taken.";
        } else { 
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO Users (username, enamil, password, role)
                VALUES (:username, :email, :password, :role
            ");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role
            ]);
            $message = "✅ User registerd successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Cybersecurity Audit Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            border-raidus: 10px;
            padding: 30px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #2c3e50; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { 
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
        .msg { text-align: center; margin-top: 10px; font-weight: bold; }
        a { color: #0078D4; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧑‍💻 Register User</h2>

        <?php if ($message): ?>
            <p class="msg"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confrim Password:</label>
            <input type="password" name="confirm_password" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="analyst">Analyst</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Register</button>
        </form>

        <p style="text-align:center;margin-top:15px;">
            <a href="index.php">⬅ Back to Login</a>
        </p>
    </div>
</body>
</html>