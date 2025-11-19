<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized")
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

// Prepare CSV download 
$filename = "audit_logs_" . date("Y-m-d_H-i-s") . ".csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$filename");

$output = fopen("php://output", "w");

// CSV column headers
fputcsv($output, ["User", "Action", "Details", "Date/Time"]);

// Data rows 
foreach ($logs as $log) { 
    fputcsv($output, [
        $log['username'] ?? "Unkown",
        $log['action'],
        $log['details'],
        $log['action_time']
    ]);
}

fclose($output);
exit();