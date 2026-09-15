<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();
$action = $_GET['action'] ?? 'search';

if ($action==='search') {
    $q = $_GET['q'] ?? '';
    if(strlen($q)<1) jsonResponse(['success'=>true,'data'=>[]]);
    $stmt = $pdo->prepare("SELECT id,name,calories,protein,carbs,fat FROM foods WHERE name LIKE ? ORDER BY name LIMIT 30");
    $stmt->execute(["%{$q}%"]);
    jsonResponse(['success'=>true,'data'=>$stmt->fetchAll()]);

} elseif ($action==='add') {
    $input = json_decode(file_get_contents('php://input'),true);
    $name = trim($input['name']??'');
    if(!$name) jsonResponse(['success'=>false,'message'=>'نام غذا را وارد کنید']);
    $stmt = $pdo->prepare("INSERT INTO foods (user_id,name,calories,protein,carbs,fat,fiber,sugar,calcium,iron,sodium,potassium,vitamin_a,vitamin_c,vitamin_d,cholesterol) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$userId,$name,$input['calories']??0,$input['protein']??0,$input['carbs']??0,$input['fat']??0,$input['fiber']??0,$input['sugar']??0,$input['calcium']??0,$input['iron']??0,$input['sodium']??0,$input['potassium']??0,$input['vitamin_a']??0,$input['vitamin_c']??0,$input['vitamin_d']??0,$input['cholesterol']??0]);
    jsonResponse(['success'=>true,'message'=>'غذا اضافه شد ✅']);

} elseif ($action==='detail') {
    $id = intval($_GET['id']??0);
    $stmt = $pdo->prepare("SELECT * FROM foods WHERE id=?");
    $stmt->execute([$id]);
    jsonResponse(['success'=>true,'data'=>$stmt->fetch()]);
}
