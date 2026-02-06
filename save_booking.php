<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
    exit;
}

$name = htmlspecialchars($data['name'] ?? 'Unknown');
$phone = htmlspecialchars($data['phone'] ?? 'Unknown');
$message = htmlspecialchars($data['message'] ?? 'N/A');
$timestamp = date('Y-m-d H:i:s');

// Format the entry for the master log
$log_entry = "--- NEW BOOKING REQUEST ---\n";
$log_entry .= "Time: $timestamp\n";
$log_entry .= "Name: $name\n";
$log_entry .= "Phone: $phone\n";
$log_entry .= "Note: $message\n";
$log_entry .= "----------------------------\n\n";

// 1. Save to a master log file
$master_file = 'leads_log.txt';
$saved_to_master = file_put_contents($master_file, $log_entry, FILE_APPEND | LOCK_EX);

// 2. Save to an individual file for this lead
$safe_name = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
$individual_file = 'booking_' . $safe_name . '_' . time() . '.txt';
$saved_to_individual = file_put_contents($individual_file, $log_entry);

if ($saved_to_master !== false) {
    // Optionally: Use PHP mail() function to send an email as well
    $to = "info@systemnextit.com";
    $subject = "New Lead: " . $name;
    $headers = "From: webmaster@systemnextit.com";
    mail($to, $subject, $log_entry, $headers);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Permission denied. Check folder permissions in cPanel.']);
}
?>