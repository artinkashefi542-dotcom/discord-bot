<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'message' => 'POST only']);
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
if (!$message) jsonResponse(['success' => false, 'message' => 'empty']);

// Convert Persian numbers
$message = str_replace(['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], ['0','1','2','3','4','5','6','7','8','9'], $message);

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$firstName = $user['first_name'] ?? 'کاربر';

// Today's stats
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COALESCE(SUM(mf.calories),0) as cal, COALESCE(SUM(mf.protein),0) as pro, COALESCE(SUM(mf.carbs),0) as carb, COALESCE(SUM(mf.fat),0) as fat FROM meals m JOIN meal_foods mf ON mf.meal_id=m.id WHERE m.user_id=? AND m.date=?");
$stmt->execute([$userId, $today]);
$tStats = $stmt->fetch();

// Food cache check
$aiFoodWords = ['غذا','صبحانه','ناهار','شام','میوه','گوشت','مرغ','ماهی','برنج','نان','پنیر','ماست','تخم‌مرغ','شیر','قهوه','ساندویچ','پیتزا','سالاد','سوپ','آبگوشت','قورمه'];
$isFood = false;
foreach ($aiFoodWords as $kw) { if (mb_strpos($message, $kw) !== false) { $isFood = true; break; } }
if (preg_match('/[\d۰-۹]+\s*گرم/', $message, $m)) $isFood = true;

if ($isFood) {
    $grams = 100;
    if (preg_match('/(\d+)\s*گرم/', $message, $gm)) $grams = intval($gm[1]);
    $cacheKey = strtolower(trim($message)) . '_' . intval($grams);
    $stmt = $pdo->prepare("SELECT data_json FROM food_cache WHERE search_key = ?");
    $stmt->execute([$cacheKey]);
    $cached = $stmt->fetch();
    if ($cached) {
        $fd = json_decode($cached['data_json'], true);
        jsonResponse(['success' => true, 'data' => [
            'reply' => "🍽️ <b>{$fd['food_name']}</b> ({$fd['per_grams']}g)\n🔥 کالری: {$fd['calories']} kcal\n🥩 پروتئین: {$fd['protein']}g\n🌾 کربو: {$fd['carbs']}g\n🫒 چربی: {$fd['fat']}g\n🦴 کلسیم: {$fd['calcium']}mg | 🩸 آهن: {$fd['iron']}mg"
        ]]);
        exit;
    }
}

// ═══════ AI CALL — IT DECIDES EVERYTHING ═══════
$apiKey = AI_API_KEY;
$apiUrl = AI_API_URL;
$model = AI_MODEL;

$systemPrompt = <<<EOT
تو ایجنت هوش مصنوعی AK Fitness Pro هستی. کاربر {$firstName} هست.

تو دو کار انجام میدی:

۱. پاسخ به سوالات تغذیه و ورزش — به فارسی صمیمی و کوتاه.

۲. تغییر اطلاعات کاربر — وقتی کاربر خواست چیزی رو عوض کنه، یه بلوک JSON برگردون.

⚠️ قانون مهم: وقتی کاربر هر چیزی راجع به تغییر هدف، وزن، قد، سن، یا هر عدد شخصی‌اش بگه (حتی غیرمستقیم)، حتماً بلوک JSON برگردون.

بلوک JSON رو دقیقاً اینطوری بنویس:
```ACTION_JSON
{"action":"update","field":"daily_calories","value":2500}
```

فیلدهای مجاز:
- daily_calories (کالری)
- daily_protein (پروتئین)
- daily_carbs (کربوهیدرات)
- daily_fat (چربی)
- weight (وزن)
- height (قد)
- age (سن)

مثال‌ها:
- "کالریم رو ۲۵۰۰ کن" → بازگردان JSON + پاسخ صمیمی
- "میخوام پروتئینم ۱۳۰ باشه" → بازگردان JSON + پاسخ
- "وزنم ۸۵ شده" → بازگردان JSON + پاسخ
- "-goal calories 2000" → بازگردان JSON + پاسخ
- "هدفم رو عوض کن" → اگه عدد داده JSON بده، اگه نه بپرس چقدر
- "چند کالری خوردم؟" → از اطلاعاتش بگو: هدف {$user['daily_calories']} kcal، مصرف امروز {$tStats['cal']} kcal
- "۱۰۰ گرم برنج" → اطلاعات تغذیه‌ای (بدون JSON)

اطلاعات فعلی کاربر:
- وزن: {$user['weight']}kg | قد: {$user['height']}cm | سن: {$user['age']}
- هدف: {$user['daily_calories']} کالری | {$user['daily_protein']}g پروتئین | {$user['daily_carbs']}g کربو | {$user['daily_fat']}g چربی
- مصرف امروز: {$tStats['cal']} کالری | {$tStats['pro']}g پروتئین | {$tStats['carb']}g کربو | {$tStats['fat']}g چربی

فقط به موضوعات تغذیه، ورزش، سلامتی پاسخ بده. بقیه رو مؤدبانه رد کن.
EOT;

$ch = curl_init($apiUrl);
$authVal = 'Bearer ' . $apiKey;
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: ' . $authVal],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $message],
        ],
        'temperature' => 0.7,
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
    $content = trim($result['choices'][0]['message']['content'] ?? '');
    
    // Check if AI returned an ACTION_JSON block
    $goalChanged = false;
    $goalField = null;
    $goalValue = null;
    
    if (preg_match('/```ACTION_JSON\s*\n?(.*?)\n?```/s', $content, $am)) {
        $actionData = json_decode(trim($am[1]), true);
        if ($actionData && $actionData['action'] === 'update' && !empty($actionData['field']) && isset($actionData['value'])) {
            $allowedFields = ['daily_calories','daily_protein','daily_carbs','daily_fat','weight','height','age'];
            if (in_array($actionData['field'], $allowedFields) && $actionData['value'] > 0) {
                $stmt = $pdo->prepare("UPDATE users SET `{$actionData['field']}` = ? WHERE id = ?");
                $stmt->execute([$actionData['value'], $userId]);
                $goalChanged = true;
                $goalField = $actionData['field'];
                $goalValue = $actionData['value'];
            }
        }
    }
    
    // Clean the response — remove ACTION_JSON block from display
    $content = preg_replace('/```ACTION_JSON[\s\S]*?```/', '', $content);
    $content = preg_replace('/#{1,6}\s*/', '', $content);
    $content = preg_replace('/\*\*(.+?)\*\*/', '$1', $content);
    $content = preg_replace('/\*(.+?)\*/', '$1', $content);
    $content = trim($content);
    
    if ($goalChanged) {
        $fieldNames = ['daily_calories'=>'🎯 هدف کالری','daily_protein'=>'🥩 هدف پروتئین','daily_carbs'=>'🌾 هدف کربو','daily_fat'=>'🫒 هدف چربی','weight'=>'⚖️ وزن','height'=>'📏 قد','age'=>'🎂 سن'];
        $fname = $fieldNames[$goalField] ?? $goalField;
        if (empty($content)) $content = "✅ {$fname} به {$goalValue} تغییر کرد! 💪";
        jsonResponse(['success' => true, 'data' => ['reply' => $content, 'goal_changed' => true, 'field' => $goalField, 'value' => $goalValue]]);
        exit;
    }
    
    if ($content) {
        jsonResponse(['success' => true, 'data' => ['reply' => $content]]);
        exit;
    }
}

jsonResponse(['success' => true, 'data' => ['reply' => 'متأسفم، الان نمی‌تونم پاسخ بدم. لطفاً دوباره امتحان کن. ⚠️']]);
