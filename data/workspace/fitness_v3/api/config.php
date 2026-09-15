<?php
/**
 * Fitness Website – config.php
 * PDO connection, table auto-creation, session, helper functions.
 * UTF-8 throughout | Persian UI text
 */

// ── Timezone ────────────────────────────────────────────────
date_default_timezone_set('Asia/Tehran');

// ── Session ─────────────────────────────────────────────────
// Keep user logged in for 30 days (must be before session_start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
    ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
    session_start();
}

// ── PDO Connection ──────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'h409204_fitness');
define('DB_USER', 'h409204_fitness');
define('DB_PASS', 'FitPr0_2026!');
define('DB_CHARSET', 'utf8mb4');

// ── AI API (loaded from api_key.php) ───────────────────────
include __DIR__ . '/api_key.php';
define('AI_API_KEY', $AI_KEY);
define('AI_API_URL', $AI_URL);
define('AI_MODEL', $AI_MODEL);

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'اتصال به پایگاه داده امکان‌پذیر نیست']);
    exit;
}

// ── Auto-create tables on first include ─────────────────────
function createTables(PDO $pdo): void
{
    $charset = 'utf8mb4';
    $collate = 'utf8mb4_persian_ci';

    $sql = [];

    $sql['users'] = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) UNIQUE NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `first_name` VARCHAR(100) DEFAULT NULL,
        `last_name` VARCHAR(100) DEFAULT NULL,
        `weight` FLOAT DEFAULT 0,
        `height` FLOAT DEFAULT 0,
        `age` INT DEFAULT 0,
        `gender` ENUM('male','female') DEFAULT 'male',
        `daily_calories` INT DEFAULT 0,
        `daily_protein` INT DEFAULT 0,
        `daily_carbs` INT DEFAULT 0,
        `daily_fat` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['foods'] = "CREATE TABLE IF NOT EXISTS `foods` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT DEFAULT NULL,
        `name` VARCHAR(255) NOT NULL,
        `calories` FLOAT DEFAULT 0,
        `protein` FLOAT DEFAULT 0,
        `carbs` FLOAT DEFAULT 0,
        `fat` FLOAT DEFAULT 0,
        `fiber` FLOAT DEFAULT 0,
        `sugar` FLOAT DEFAULT 0,
        `calcium` FLOAT DEFAULT 0,
        `iron` FLOAT DEFAULT 0,
        `sodium` FLOAT DEFAULT 0,
        `potassium` FLOAT DEFAULT 0,
        `vitamin_a` FLOAT DEFAULT 0,
        `vitamin_c` FLOAT DEFAULT 0,
        `vitamin_d` FLOAT DEFAULT 0,
        `cholesterol` FLOAT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_user_id` (`user_id`),
        INDEX `idx_name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['meals'] = "CREATE TABLE IF NOT EXISTS `meals` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `meal_type` VARCHAR(20) DEFAULT NULL COMMENT 'صبحانه/ناهار/شام/میان‌وعده',
        `date` DATE NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_user_date` (`user_id`, `date`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['meal_foods'] = "CREATE TABLE IF NOT EXISTS `meal_foods` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `meal_id` INT NOT NULL,
        `food_name` VARCHAR(255) DEFAULT NULL,
        `grams` FLOAT DEFAULT 0,
        `calories` FLOAT DEFAULT 0,
        `protein` FLOAT DEFAULT 0,
        `carbs` FLOAT DEFAULT 0,
        `fat` FLOAT DEFAULT 0,
        `fiber` FLOAT DEFAULT 0,
        `sugar` FLOAT DEFAULT 0,
        `calcium` FLOAT DEFAULT 0,
        `iron` FLOAT DEFAULT 0,
        `sodium` FLOAT DEFAULT 0,
        `potassium` FLOAT DEFAULT 0,
        `vitamin_a` FLOAT DEFAULT 0,
        `vitamin_c` FLOAT DEFAULT 0,
        `vitamin_d` FLOAT DEFAULT 0,
        `cholesterol` FLOAT DEFAULT 0,
        INDEX `idx_meal_id` (`meal_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['food_cache'] = "CREATE TABLE IF NOT EXISTS `food_cache` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `search_key` VARCHAR(255) UNIQUE NOT NULL,
        `food_name` VARCHAR(255) DEFAULT NULL,
        `data_json` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['groups'] = "CREATE TABLE IF NOT EXISTS `groups` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) DEFAULT NULL,
        `code` VARCHAR(8) UNIQUE NOT NULL,
        `creator_id` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['group_members'] = "CREATE TABLE IF NOT EXISTS `group_members` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `group_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_membership` (`group_id`, `user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    $sql['agent_messages'] = "CREATE TABLE IF NOT EXISTS `agent_messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `message` TEXT DEFAULT NULL,
        `response` TEXT DEFAULT NULL,
        `status` ENUM('pending','done','not_found') DEFAULT 'pending',
        `food_data` TEXT DEFAULT NULL COMMENT 'JSON nutrition result',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate}";

    foreach ($sql as $table => $ddl) {
        $pdo->exec($ddl);
    }
}

createTables($pdo);

// ── Helper Functions ────────────────────────────────────────

/**
 * Send a JSON response and exit.
 *
 * @param mixed  $data    Data to encode as JSON
 * @param int    $code    HTTP status code (default 200)
 * @param bool   $success Whether the response represents success
 */
function jsonResponse($data, int $code = 200, bool $success = true): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Require a logged-in user; exit with 401 if not authenticated.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'error' => 'لطفاً ابتدا وارد شوید'], 401, false);
    }
}

/**
 * Check whether a user is currently logged in.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/**
 * Get the current user's ID, or 0 if not logged in.
 */
function getUserId(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}
