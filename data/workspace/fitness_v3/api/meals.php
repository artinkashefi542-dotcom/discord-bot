<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();
$action = $_GET['action'] ?? 'list';

if ($action==='list') {
    $date = $_GET['date'] ?? date('Y-m-d');
    $stmt = $pdo->prepare("SELECT m.id,m.meal_type,m.date,m.created_at FROM meals m WHERE m.user_id=? AND m.date=? ORDER BY m.created_at DESC");
    $stmt->execute([$userId,$date]);
    $meals = $stmt->fetchAll();
    foreach($meals as &$m){
        $stmt2 = $pdo->prepare("SELECT * FROM meal_foods WHERE meal_id=?");
        $stmt2->execute([$m['id']]);
        $m['foods'] = $stmt2->fetchAll();
        $m['total_calories'] = array_sum(array_column($m['foods'],'calories'));
        $m['total_protein'] = array_sum(array_column($m['foods'],'protein'));
    }
    jsonResponse(['success'=>true,'data'=>$meals]);

} elseif ($action==='history') {
    $days = intval($_GET['days'] ?? 14);
    $stmt = $pdo->prepare("SELECT DISTINCT m.date FROM meals m WHERE m.user_id=? ORDER BY m.date DESC LIMIT ?");
    $stmt->execute([$userId,$days]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $history = [];
    foreach($dates as $date){
        $stmt = $pdo->prepare("SELECT m.id,m.meal_type,m.created_at FROM meals m WHERE m.user_id=? AND m.date=?");
        $stmt->execute([$userId,$date]);
        $meals = $stmt->fetchAll();
        foreach($meals as &$m){
            $stmt2 = $pdo->prepare("SELECT * FROM meal_foods WHERE meal_id=?");
            $stmt2->execute([$m['id']]);
            $m['foods'] = $stmt2->fetchAll();
            $m['total_calories'] = array_sum(array_column($m['foods'],'calories'));
            $m['total_protein'] = array_sum(array_column($m['foods'],'protein'));
        }
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(calories),0) as cal,COALESCE(SUM(protein),0) as pro,COALESCE(SUM(carbs),0) as carb,COALESCE(SUM(fat),0) as fat FROM meal_foods mf JOIN meals m ON mf.meal_id=m.id WHERE m.user_id=? AND m.date=?");
        $stmt->execute([$userId,$date]);
        $t = $stmt->fetch();
        $history[] = ['date'=>$date,'meals'=>$meals,'totals'=>$t];
    }
    jsonResponse(['success'=>true,'data'=>$history]);

} elseif ($action==='add') {
    $input = json_decode(file_get_contents('php://input'),true);
    $mealType = $input['meal_type'] ?? 'شام';
    $foods = $input['foods'] ?? [];
    $date = $input['date'] ?? date('Y-m-d');
    if(empty($foods)) jsonResponse(['success'=>false,'message'=>'حداقل یک غذا اضافه کنید']);
    
    $stmt = $pdo->prepare("INSERT INTO meals (user_id,meal_type,date) VALUES (?,?,?)");
    $stmt->execute([$userId,$mealType,$date]);
    $mealId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO meal_foods (meal_id,food_name,grams,calories,protein,carbs,fat,fiber,sugar,calcium,iron,sodium,potassium,vitamin_a,vitamin_c,vitamin_d,cholesterol) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    foreach($foods as $f){
        $stmt->execute([$mealId,$f['food_name'],$f['grams']??100,$f['calories']??0,$f['protein']??0,$f['carbs']??0,$f['fat']??0,$f['fiber']??0,$f['sugar']??0,$f['calcium']??0,$f['iron']??0,$f['sodium']??0,$f['potassium']??0,$f['vitamin_a']??0,$f['vitamin_c']??0,$f['vitamin_d']??0,$f['cholesterol']??0]);
    }
    jsonResponse(['success'=>true,'message'=>'وعده ثبت شد ✅','data'=>['meal_id'=>$mealId]]);

} elseif ($action==='delete') {
    $input = json_decode(file_get_contents('php://input'),true);
    $mealId = $input['meal_id'] ?? 0;
    $pdo->prepare("DELETE FROM meal_foods WHERE meal_id=?")->execute([$mealId]);
    $pdo->prepare("DELETE FROM meals WHERE id=? AND user_id=?")->execute([$mealId,$userId]);
    jsonResponse(['success'=>true,'message'=>'حذف شد']);
}
