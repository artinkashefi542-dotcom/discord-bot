<?php
require_once __DIR__ . '/config.php';
$action = $_GET['action'] ?? '';

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) jsonResponse(['success'=>false,'message'=>'فیلدها را پر کنید']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user || $user['password'] !== $password) {
        jsonResponse(['success'=>false,'message'=>'نام کاربری یا رمز اشتباه است']);
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    unset($user['password']);
    jsonResponse(['success'=>true,'message'=>'ورود موفق','data'=>$user]);

} elseif ($action === 'register') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $weight = floatval($_POST['weight'] ?? 0);
    $height = floatval($_POST['height'] ?? 0);
    $age = intval($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? 'male';
    
    if (!$username || !$password || !$first_name) jsonResponse(['success'=>false,'message'=>'فیلدهای ضروری را پر کنید']);
    if (strlen($username) < 3) jsonResponse(['success'=>false,'message'=>'نام کاربری حداقل ۳ کاراکتر']);
    if (strlen($password) < 4) jsonResponse(['success'=>false,'message'=>'رمز حداقل ۴ کاراکتر']);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) jsonResponse(['success'=>false,'message'=>'نام کاربری تکراری است']);
    
    $bmr = ($gender === 'male') ? 10*$weight + 6.25*$height - 5*$age + 5 : 10*$weight + 6.25*$height - 5*$age - 161;
    $calories = round($bmr * 1.5);
    $protein = round($weight * 1.8);
    $carbs = round($calories * 0.5 / 4);
    $fat = round($calories * 0.3 / 9);
    
    $stmt = $pdo->prepare("INSERT INTO users (username,password,first_name,last_name,weight,height,age,gender,daily_calories,daily_protein,daily_carbs,daily_fat) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$username,$password,$first_name,$last_name,$weight,$height,$age,$gender,$calories,$protein,$carbs,$fat]);
    
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['username'] = $username;
    
    jsonResponse(['success'=>true,'message'=>'ثبت‌نام موفق','data'=>[
        'id'=>$_SESSION['user_id'],'username'=>$username,'first_name'=>$first_name,'last_name'=>$last_name,
        'daily_calories'=>$calories,'daily_protein'=>$protein,'daily_carbs'=>$carbs,'daily_fat'=>$fat
    ]]);

} elseif ($action === 'logout') {
    session_destroy();
    jsonResponse(['success'=>true,'message'=>'خروج موفق']);

} elseif ($action === 'check') {
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT id,username,first_name,last_name,weight,height,age,gender,daily_calories,daily_protein,daily_carbs,daily_fat FROM users WHERE id=?");
        $stmt->execute([getUserId()]);
        jsonResponse(['success'=>true,'data'=>$stmt->fetch()]);
    } else {
        jsonResponse(['success'=>false]);
    }
} else {
    jsonResponse(['success'=>false,'message'=>'action نامعتبر']);
}
