<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized")
}

$severity = $_GET['severity'] ?? '';
$status = $_GET['status'] ?? '';

// Build query 
$query = "
    SELECT v.*, u.username AS created_by_name
    FROM Vulnerabilities v
    LEFT JOIN Users u ON v.created_by = u.user_id
    WHERE 1=1
";

$params = [];

if (!empty($severity)) {
    $query .= " AND v.severity LIKE :severity";
    $params['severity'] = $severity;
}

if (!empty($status)) {
    $query .= " AND v.status LIKE :status";
    $params['status'] = $status;
}

$query .= " ORDER BY v.date_found DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare CSV download 
$filename = "vulnerabilities_" . date("Y-m-d_H-i-s") . ".csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$filename");

$output = fopen("php://output", "w");

// Column headers 
fputcsv($output, [
    "ID", "Title", "Severity", "Status", "System Affected",
    "Description", "Date Found", "Created By"
]);

// Data rows 
foreach ($rows as $r) {
    fputcsv($output, [
        $r['vuln_id'],
        $r['title'],
        $r['severity'],
        $r['status'],
        $r['system_affected'],
        $r['description'],
        $r['date_found'],
        $r['created_by_name'] ?? "Unkown"
    ]);
}

fclose($output);
exit();