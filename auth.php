<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$usersFile = 'users_data.json';
$logFile = 'auth_log.txt';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Response function
function sendResponse($success, $message = '', $extraData = []) {
    $response = array_merge(['success' => $success], $extraData);
    if ($message) {
        $response[$success ? 'message' : 'error'] = $message;
    }
    echo json_encode($response);
    exit;
}

// Log function
function logActivity($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Validate input
if (!$data || !isset($data['action'])) {
    logActivity('Invalid request - no action specified');
    sendResponse(false, 'Invalid request');
}

$action = $data['action'];

// Load existing users
$users = [];
if (file_exists($usersFile)) {
    $usersJson = file_get_contents($usersFile);
    $users = json_decode($usersJson, true) ?: [];
}

// Handle REGISTRATION
if ($action === 'register') {
    // Validate required fields
    if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['password'])) {
        logActivity('Registration failed - missing fields');
        sendResponse(false, 'All fields are required');
    }

    $name = trim($data['name']);
    $email = strtolower(trim($data['email']));
    $phone = trim($data['phone']);
    $password = $data['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logActivity("Registration failed - invalid email: $email");
        sendResponse(false, 'Invalid email format');
    }

    // Validate password length
    if (strlen($password) < 6) {
        logActivity("Registration failed - password too short for: $email");
        sendResponse(false, 'Password must be at least 6 characters');
    }

    // Check if email already exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            logActivity("Registration failed - email already exists: $email");
            sendResponse(false, 'Email already registered');
        }
    }

    // Create new user
    $newUser = [
        'id' => uniqid('user_'),
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'registered_at' => date('Y-m-d H:i:s'),
        'last_login' => null
    ];

    // Add to users array
    $users[] = $newUser;

    // Save to file
    if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
        logActivity("Registration successful - User: $name, Email: $email");
        sendResponse(true, 'Account created successfully', ['name' => $name]);
    } else {
        logActivity("Registration failed - could not save file for: $email");
        sendResponse(false, 'Could not save user data');
    }
}

// Handle SIGN IN
if ($action === 'signin') {
    // Validate required fields
    if (empty($data['email']) || empty($data['password'])) {
        logActivity('Sign in failed - missing credentials');
        sendResponse(false, 'Email and password are required');
    }

    $email = strtolower(trim($data['email']));
    $password = $data['password'];

    // Find user
    $foundUser = null;
    $userIndex = null;
    foreach ($users as $index => $user) {
        if ($user['email'] === $email) {
            $foundUser = $user;
            $userIndex = $index;
            break;
        }
    }

    // Check if user exists
    if (!$foundUser) {
        logActivity("Sign in failed - user not found: $email");
        sendResponse(false, 'Invalid email or password');
    }

    // Verify password
    if (!password_verify($password, $foundUser['password'])) {
        logActivity("Sign in failed - wrong password for: $email");
        sendResponse(false, 'Invalid email or password');
    }

    // Update last login
    $users[$userIndex]['last_login'] = date('Y-m-d H:i:s');
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

    logActivity("Sign in successful - User: {$foundUser['name']}, Email: $email");
    sendResponse(true, 'Sign in successful', [
        'name' => $foundUser['name'],
        'email' => $foundUser['email'],
        'user_id' => $foundUser['id']
    ]);
}

// Invalid action
logActivity("Invalid action: $action");
sendResponse(false, 'Invalid action');
?>
