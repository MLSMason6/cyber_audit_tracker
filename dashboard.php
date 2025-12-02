<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$totalQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities");
$total = $totalQuery->fetchColumn();

$openQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE status = 'Open'");
$open = $openQuery->fetchColumn();

$progressQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE status = 'In Progress'");
$progress = $progressQuery->fetchColumn();

$resolvedQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE status = 'Resolved'");
$resolved = $resolvedQuery->fetchColumn();

$lowQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE severity = 'Low'");
$low = $lowQuery->fetchColumn();

$mediumQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE severity = 'Medium'");
$medium = $mediumQuery->fetchColumn();

$highQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE severity = 'High'");
$high = $highQuery->fetchColumn();

$criticalQuery = $pdo->query("SELECT COUNT(*) FROM Vulnerabilities WHERE severity = 'Critical'");
$critical = $criticalQuery->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Cybersecurity Audit Tracker</title>

    <!-- Load Chart.js FIRST -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            max-width: 1000px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px; /* fixed typo */
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { 
            color: #2c3e50;
            text-align: center; 
        }
        .summary {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .card {
            background: #f7f9fb; /* fixed missing colon */
            border-radius: 8px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 5px 0;
        }
        .chart-container { 
            width: 400px;
            margin: 40px auto; /* fixed typo */
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            margin: 0 10px;
            color: #0078D4;
            text-decoration: none;
            font-weight: bold;
        }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>🛡️ Cybersecurity Audit Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
        <p><a href="logout.php" style="color:yellow;text-decoration:none;">Logout</a></p>
    </header>

    <div class="container">
        <h2>System Overview</h2>
        <div class="summary">
            <div class="card">
                <h3>Total</h3>
                <p><?= $total ?></p>
            </div>
            <div class="card">
                <h3>Open</h3>
                <p><?= $open ?></p>
            </div>
            <div class="card">
                <h3>In Progress</h3>
                <p><?= $progress ?></p>
            </div>
            <div class="card">
                <h3>Resolved</h3>
                <p><?= $resolved ?></p>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="severityChart"></canvas>
        </div>

        <div class="links">
            <a href="add_vulnerability.php">➕ Add Vulnerability</a>
            <a href="manage_vulnerabilities.php">📋 Manage Vulnerabilities</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="register.php">👤 Manage Users</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('statusChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'In Progress', 'Resolved'],
                datasets: [{
                    label: 'Vulnerability Status',
                    data: [<?= $open ?>, <?= $progress ?>, <?= $resolved ?>],
                    backgroundColor: ['#e74c3c', '#f1c40f', '#2ecc71']
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' } /* fixed typo */
                }
            }
        });
    </script>

    <script>
        const severityCtx = document.getElementById('severityChart');

        new Chart(severityCtx, {
            type: 'doughnut', 
            data: {
                labels: ['Low', 'Medium', 'High', 'Critical'],
                datasets: [{
                    label: 'Severity Levels',
                    data: [<?= $low ?>, <?= $medium ?>, <?= $high ?>, <?= $critical ?>],
                    backgroundColor: [
                    '#2ecc71',   // Low - green
                    '#f1c40f',   // Medium - yellow
                    '#e67e22',   // High - orange
                    '#e74c3c'    // Critical - red 
                    ]
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>

</body>
</html>