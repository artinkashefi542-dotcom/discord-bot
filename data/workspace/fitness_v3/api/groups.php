<?php
require_once __DIR__ . '/config.php';
requireAuth();
$userId = getUserId();
$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT g.id, g.name, g.code, g.created_at FROM `groups` g JOIN group_members gm ON gm.group_id=g.id WHERE gm.user_id=?");
    $stmt->execute([$userId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);

} elseif ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'message' => 'فقط POST']);
    $input = json_decode(file_get_contents('php://input'), true);
    $name = trim($input['name'] ?? '');
    if (!$name) jsonResponse(['success' => false, 'message' => 'نام گروه را وارد کنید']);
    $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    $stmt = $pdo->prepare("INSERT INTO `groups` (name, code, creator_id) VALUES (?, ?, ?)");
    $stmt->execute([$name, $code, $userId]);
    $groupId = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)")->execute([$groupId, $userId]);
    jsonResponse(['success' => true, 'message' => 'ساخته شد', 'data' => ['group_id' => $groupId, 'code' => $code]]);

} elseif ($action === 'join') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'message' => 'فقط POST']);
    $input = json_decode(file_get_contents('php://input'), true);
    $code = strtoupper(trim($input['code'] ?? ''));
    if (!$code) jsonResponse(['success' => false, 'message' => 'کد گروه را وارد کنید']);
    $stmt = $pdo->prepare("SELECT * FROM `groups` WHERE code = ?");
    $stmt->execute([$code]);
    $group = $stmt->fetch();
    if (!$group) jsonResponse(['success' => false, 'message' => 'گروه پیدا نشد']);
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id=? AND user_id=?");
    $stmt->execute([$group['id'], $userId]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'عضو گروه هستید']);
    $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)")->execute([$group['id'], $userId]);
    jsonResponse(['success' => true, 'message' => 'عضو شدید ✅']);

} elseif ($action === 'detail') {
    $groupId = intval($_GET['group_id'] ?? 0);
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, u.username FROM users u JOIN group_members gm ON gm.user_id=u.id WHERE gm.group_id=?");
    $stmt->execute([$groupId]);
    $members = $stmt->fetchAll();
    foreach ($members as &$m) {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(calories),0) as cal, COALESCE(SUM(protein),0) as pro, COALESCE(SUM(carbs),0) as carb, COALESCE(SUM(fat),0) as fat FROM meal_foods mf JOIN meals me ON mf.meal_id=me.id WHERE me.user_id=? AND me.date=?");
        $stmt->execute([$m['id'], $today]);
        $m['today'] = $stmt->fetch();
    }
    usort($members, function ($a, $b) { return $b['today']['pro'] <=> $a['today']['pro']; });
    jsonResponse(['success' => true, 'data' => ['members' => $members]]);

} elseif ($action === 'history') {
    $userId2 = intval($_GET['member_id'] ?? 0);
    $days = intval($_GET['days'] ?? 7);
    $stmt = $pdo->prepare("SELECT DISTINCT me.date FROM meal_foods mf JOIN meals me ON mf.meal_id=me.id WHERE me.user_id=? ORDER BY me.date DESC LIMIT ?");
    $stmt->execute([$userId2, $days]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $history = [];
    foreach ($dates as $d) {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(calories),0) as cal, COALESCE(SUM(protein),0) as pro, COALESCE(SUM(carbs),0) as carb, COALESCE(SUM(fat),0) as fat FROM meal_foods mf JOIN meals me ON mf.meal_id=me.id WHERE me.user_id=? AND me.date=?");
        $stmt->execute([$userId2, $d]);
        $history[] = array_merge(['date' => $d], $stmt->fetch());
    }
    jsonResponse(['success' => true, 'data' => $history]);
}
