<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();
$action = $_GET['action'] ?? 'get';

if ($action === 'get') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    unset($user['password']);
    jsonResponse(['success' => true, 'data' => $user]);
    exit;
}

if ($action === 'update') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) jsonResponse(['success' => false, 'message' => 'no data']);
    
    // Allowed fields
    $allowed = ['first_name','last_name','weight','height','age','gender','daily_calories','daily_protein','daily_carbs','daily_fat'];
    $sets = [];
    $vals = [];
    
    foreach ($allowed as $f) {
        if (isset($input[$f])) {
            $sets[] = "`$f` = ?";
            $vals[] = $input[$f];
        }
    }
    
    if (empty($sets)) jsonResponse(['success' => false, 'message' => 'no fields to update']);
    
    $vals[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($vals);
    
    jsonResponse(['success' => true, 'message' => '✅ ذخیره شد']);
    exit;
}

jsonResponse(['success' => false, 'message' => 'unknown action']);
