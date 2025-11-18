<?php
session_start();
require_once "includes/db_connect.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: dashboard.php");
    exit();
}

// Filters 
$userFilter = $_GET['user'] ?? '';
$actionFilter = $_GET['action'] ?? '';
$startDate = $_GET['start'] ?? '';
$endDate = $_GET['end'] ?? '';

// Build query 
$query = "
    SELECT log.*, u.username
    FROM AuditLog log
    LEFT JOIN Users u ON log.user_id = u.user_id
    WHERE 1=1
";

$params = [];

if (!empty($userFilter)) {
    $query .= " AND u.username LIKE :user";
    $params['user'] = "%$userFilter";
}

if (!empty($actionFilter)) {
    $query .= " AND log.action LIKE :action";
    $params['action'] = "%$actionFilter";
}

if (!empty($startDate)) {
    $query .= " AND DATE(log.action_time) >= :start";
    $params['start'] = $startDate;
}

if (!empty($endDate)) {
    $query .= " AND DATE(log.action_time) <= :end";
    $params['end'] = $endDate;
}

$query .= " ORDER BY log.action_time DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs - Cybersecurity Audit Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-raidus: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { color: #2c3e50; }
        table { 
            width: 100% 
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th { 
            background: #0078D4;
            color: white;
        }
        tr:nth-child(even) { background: #f9f9f9; }
        .filters { 
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        input, button { 
            padding: 8px;
            border-raidus: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #0078D4;
            color: white;
            cursor: pointer;
        }
        button:hover { background: #005fa3; }
        a { color: #0078D4; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>🛡️ Cybersecurity Audit Tracker</h1>
        <p>Admin: <?= htmlspecialchars($_SESSION['username']) ?></p>
        <p><a href="dashboard.php" style="color:yellow;text-decoration:none;">⬅ Dashboard</a></p>
    </header>

    <div class="container">
        <h2>Audit Log History</h2>

        <!-- Filter Form -->
        <form method="GET" class="filters">
            <div>
                <label>User</label><br>
                <input type="text" name="user" value="<?= htmlspecialchars($userFilter)?>" placeholder="Search username">
            </div>

            <div>
                <label>Action</label><br>
                <input type="text" name="action" value="<?= htmlspecialchars($actionFilter)?>" placeholder="Search actions">
            </div>

            <div>
                <label>Start Date</label><br>
                <input type="date" name="start" value="<?= htmlspecialchars($startDate)?>">
            </div>

            <div>
                <label>End Date</label><br>
                <input type="date" name="end" value="<?= htmlspecialchars($endDate)?>">
            </div>

            <div>
                <button tpye="submit">Apply</button>
                <a href="view_logs.php" style="margin-left:10px;">Reset</a>
            </div>
        </form>

        <!-- Logs Table -->
        <table>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
                <th>Date & Time</th>
            </tr>

            <?php if (count($logs) > 0): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['username'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['details']) ?></td>
                        <td><?= $log['action_time'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center;">No logs found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>