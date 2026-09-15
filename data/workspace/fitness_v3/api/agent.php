<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'message' => 'POST only']);
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
$grams = floatval($input['grams'] ?? 100);
if (!$message) jsonResponse(['success' => false, 'message' => 'empty']);

$pdo->prepare("INSERT INTO agent_messages (user_id, message) VALUES (?, ?)")->execute([$userId, $message]);
$msgId = $pdo->lastInsertId();

// Cache check
$cacheKey = strtolower(trim($message)) . '_' . intval($grams);
$stmt = $pdo->prepare("SELECT data_json FROM food_cache WHERE search_key = ?");
$stmt->execute([$cacheKey]);
$cached = $stmt->fetch();
if ($cached) {
    $foodData = json_decode($cached['data_json'], true);
    jsonResponse(['success' => true, 'data' => ['food_data' => $foodData, 'status' => 'done', 'cached' => true]]);
    exit;
}

// AI call
$apiKey = AI_API_KEY;
$apiUrl = AI_API_URL;
$model = AI_MODEL;

$prompt = <<<EOT
You are a world-class nutrition expert with encyclopedic knowledge of foods from all cultures, especially Iranian/Persian cuisine and supplements.

TASK: Calculate the EXACT nutritional values for this food at the specified amount.

Food: "{$message}"
Amount: {$grams} grams

THINK STEP BY STEP:
1. What exactly is this food? (brand, type, preparation method)
2. What category does it belong to? (raw ingredient, cooked dish, supplement, snack, beverage)
3. What are the standard nutritional values per 100g for this specific food?
4. Scale to {$grams} grams

EXAMPLES of how to think:
- "مکمل وی کاله ۳۰ گرم" → This is Kalleh brand whey protein powder. Whey protein isolate is ~360kcal/100g with ~80g protein. For 30g: 108kcal, 24g protein.
- "قورمه سبزی با گوشت" → This is a Persian stew with herbs, meat, beans. ~150kcal/100g with balanced macros.
- "پپرونی پیتزا" → Italian pizza with pepperoni. ~270kcal/100g, ~11g protein, ~33g carbs, ~10g fat.
- "اسموتی موز و توت‌فرنگی" → Blended fruit drink. ~60kcal/100g, mostly carbs from fruit sugar.
- "آجیل混合" → Mixed nuts. ~600kcal/100g, high fat and protein.
- "کنسرو تن‌ماهی" → Canned tuna. ~120kcal/100g, ~26g protein, very low carb.

Return ONLY a single JSON object. No markdown, no explanation, no code blocks:
{"food_name":"نام دقیق غذا به فارسی","per_grams":{$grams},"calories":0,"protein":0,"carbs":0,"fat":0,"fiber":0,"sugar":0,"calcium":0,"iron":0,"sodium":0,"potassium":0,"vitamin_a":0,"vitamin_c":0,"vitamin_d":0,"cholesterol":0}
EOT;

$foodData = null;

$ch = curl_init($apiUrl);
$authVal = 'Bearer ' . $apiKey;
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: ' . $authVal],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => $model,
        'messages' => [['role' => 'user', 'content' => $prompt]],
        'temperature' => 0.1,
        'max_tokens' => 4000,
        'stream' => false,
    ]),
    CURLOPT_TIMEOUT => 90,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if (!$err && $response) {
    $result = json_decode($response, true);
    $content = $result['choices'][0]['message']['content'] ?? '';
    
    $content = preg_replace('/```json\s*/i', '', $content);
    $content = preg_replace('/```\s*/', '', $content);
    $content = trim($content);
    
    $parsed = json_decode($content, true);
    if ($parsed && isset($parsed['food_name'])) {
        $foodData = $parsed;
    } else {
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $parsed = json_decode($matches[0], true);
            if ($parsed && isset($parsed['food_name'])) {
                $foodData = $parsed;
            }
        }
    }
}

if ($foodData) {
    $defaults = ['food_name'=>'','per_grams'=>100,'calories'=>0,'protein'=>0,'carbs'=>0,'fat'=>0,'fiber'=>0,'sugar'=>0,'calcium'=>0,'iron'=>0,'sodium'=>0,'potassium'=>0,'vitamin_a'=>0,'vitamin_c'=>0,'vitamin_d'=>0,'cholesterol'=>0];
    $foodData = array_merge($defaults, $foodData);
    $foodData['per_grams'] = $grams;
    $json = json_encode($foodData);
    $pdo->prepare("INSERT INTO food_cache (search_key, food_name, data_json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE data_json=?")->execute([$cacheKey, $foodData['food_name'], $json, $json]);
    $pdo->prepare("UPDATE agent_messages SET response=?, food_data=?, status='done' WHERE id=?")->execute([$json, $json, $msgId]);
    jsonResponse(['success' => true, 'data' => ['food_data' => $foodData, 'status' => 'done']]);
    exit;
}

$pdo->prepare("UPDATE agent_messages SET status='pending' WHERE id=?")->execute([$msgId]);
jsonResponse(['success' => true, 'data' => ['status' => 'pending', 'msg_id' => $msgId, 'err' => $err]]);
