<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get filter values 
$filter_severity = $_GET['severity'] ?? '';
$filter_status = $_GET['status'] ?? '';

// Build dynamic query based on filters 
$query = "SELECT v.*, u.username AS created_by_name
          FROM Vulnerabilities v
          LEFT JOIN Users u ON v.created_by = u.user_id
          WHERE 1=1";

$params = [];

if (!empty($filter_severity)) { 
    $query .= "AND v.severity = :severity";
    $params['severity'] = $filter_severity;
}

if (!empty($filter_status)) { 
    $query .= "AND v.status = :status";
    $params['status'] = $filter_status;
}

$query .= " ORDER BY v.date_found DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vulns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Vulnerabilities - Cybersecurity Audit Tracker</title>
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
        h2 { text-align: center; color: #2c3e50 }
        table { 
            width: 100% 
            border-collapse: collapse;
            margin-top: 20px;
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
        tr:nth-child(even) { background: #f9f9f9 }
        a { 
            color: #0078D4;
            text-decoration: none; 
        }
        a:hover { text-decoration: underline; }
        .filters { 
            display: flex; 
            justify-content: space-between; 
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        select, button { 
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button { 
            background: #0078D4;
            color: white;
            cursor: pointer;
        }
        button:hover { background: #005fa3; }
    </style>
</head>
<body>
    <header>
        <h1>🛡️ Cybersecurity Audit Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
        <p><a href="logout.php" style="color:yellow;text-decoration:none;">Dashboard</a></p>
    </header>

    <div class="container">
        <h2>Manage Vulnerabilities</h2>

        <form method="GET" class="filters">
            <div>
                <label for="severity">Filter by Severity:</label>
                <select name="severity" id="severity">
                    <option value="">All</option>
                    <option value="Low" <?= $filter_severity=='Low'?'selected':'' ?>>Low</option>
                    <option value="Medium" <?= $filter_severity=='Medium'?'selected':'' ?>>Medium</option>
                    <option value="High" <?= $filter_severity=='High'?'selected':'' ?>>High</option>
                    <option value="Critical" <?= $filter_severity=='Critical'?'selected':'' ?>>Critical</option>
                </select>
            </div>
            <div>
                <button type="submit">Apply Filters</button>
                <a href="manage_vulnerabilities.php" style="margin-left:10px;">Reset</a>
            </div>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Severity</th>
                <th>Status</th>
                <th>System</th>
                <th>Date Found</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>

            <?php if (count($vulns)> 0): ?>
                <?php foreach ($vulns as $v): ?>
                    <tr>
                        <td><?= $v['vuln_id'] ?></td>
                        <td><?=htmlspecialchars($v['title']) ?></td>
                        <td><?= $v['severity'] ?></td>
                        <td><?= $v['status'] ?></td>
                        <td><?=htmlspecialchars($v['system_affected']) ?></td>
                        <td><?= $v['date_found'] ?></td>
                        <td><?=htmlspecialchars($v['created_by_name'] ?? 'N/A') ?></td>
                        <td>
                            <a href="view_vulnerablity.php?id=<?= $v['vuln_id'] ?>">🔍 View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No vulnerabilities found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>