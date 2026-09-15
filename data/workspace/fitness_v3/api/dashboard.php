<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT id,username,first_name,last_name,weight,height,age,gender,daily_calories,daily_protein,daily_carbs,daily_fat FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(calories),0) as cal,COALESCE(SUM(protein),0) as pro,COALESCE(SUM(carbs),0) as carb,COALESCE(SUM(fat),0) as fat FROM meal_foods mf JOIN meals m ON mf.meal_id=m.id WHERE m.user_id=? AND m.date=?");
$stmt->execute([$userId,$today]);
$totals = $stmt->fetch();

// Get meals with full food details
$stmt = $pdo->prepare("SELECT m.id,m.meal_type,m.created_at FROM meals m WHERE m.user_id=? AND m.date=? ORDER BY m.created_at DESC");
$stmt->execute([$userId,$today]);
$meals = $stmt->fetchAll();

foreach ($meals as &$meal) {
    $stmt2 = $pdo->prepare("SELECT food_name,grams,calories,protein,carbs,fat FROM meal_foods WHERE meal_id=?");
    $stmt2->execute([$meal['id']]);
    $meal['foods'] = $stmt2->fetchAll();
    $meal['mcal'] = array_sum(array_column($meal['foods'], 'calories'));
    $meal['mpro'] = array_sum(array_column($meal['foods'], 'protein'));
}
unset($meal);

$week = [];
for($i=6;$i>=0;$i--){
    $d = date('Y-m-d',strtotime("-{$i} days"));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(mf.calories),0) as cal,COALESCE(SUM(mf.protein),0) as pro FROM meal_foods mf JOIN meals m ON mf.meal_id=m.id WHERE m.user_id=? AND m.date=?");
    $stmt->execute([$userId,$d]);
    $wd = $stmt->fetch();
    $week[] = ['date'=>$d,'label'=>date('m/d',strtotime($d)),'calories'=>$wd['cal'],'protein'=>$wd['pro']];
}

jsonResponse(['success'=>true,'data'=>compact('user','totals','meals','week')]);
