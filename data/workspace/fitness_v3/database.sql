-- ============================================================
-- Fitness Website Database Schema
-- Charset: utf8mb4 | Collation: utf8mb4_persian_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Table: users
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: foods (system foods + custom user foods)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `foods` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: meals
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `meals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `meal_type` VARCHAR(20) DEFAULT NULL COMMENT 'صبحانه/ناهار/شام/میان‌وعده',
    `date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_date` (`user_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: meal_foods
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `meal_foods` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: food_cache (AI nutrition results cache)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `food_cache` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `search_key` VARCHAR(255) UNIQUE NOT NULL,
    `food_name` VARCHAR(255) DEFAULT NULL,
    `data_json` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: groups
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `groups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) DEFAULT NULL,
    `code` VARCHAR(8) UNIQUE NOT NULL,
    `creator_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: group_members
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `group_members` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_membership` (`group_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- -----------------------------------------------------------
-- Table: agent_messages (AI chat history)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `agent_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `message` TEXT DEFAULT NULL,
    `response` TEXT DEFAULT NULL,
    `status` ENUM('pending','done','not_found') DEFAULT 'pending',
    `food_data` TEXT DEFAULT NULL COMMENT 'JSON nutrition result',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- ============================================================
-- Sample System Foods (user_id = NULL)
-- ============================================================

INSERT INTO `foods` (`user_id`, `name`, `calories`, `protein`, `carbs`, `fat`, `fiber`, `sugar`, `calcium`, `iron`, `sodium`, `potassium`, `vitamin_a`, `vitamin_c`, `vitamin_d`, `cholesterol`) VALUES
(NULL, 'برنج پخته 100g',         130,  2.7,  28,  0.3,  0.4,  0,   10,   0.2,  1,    35,  0,  0,   0,   0),
(NULL, 'مرغ سینه 100g',          165,  31,   0,   3.6,  0,    0,   15,   0.7,  74,   256, 21, 0,   0.2, 85),
(NULL, 'تخم مرغ 1 عدد 50g',     78,   6,    0.6, 5,    0,    0.4, 28,   0.9,  82,   69,  270,0,   1.1, 186),
(NULL, 'نان سنگک 100g',         260,  9,    50,  2,    3,    2,   20,   2.5,  480,  120, 0,  0,   0,   0),
(NULL, 'ماست 100g',              61,   3.5,  4.7, 3.3,  0,    4.7, 121,  0,    45,   155, 17, 0.5, 0.1, 11),
(NULL, 'ماست پرچرب 100g',        100,  4,    4,   8,    0,    4,   140,  0,    60,   170, 25, 0.5, 0.1, 24),
(NULL, 'ماست کم‌چرب 100g',       50,   3.5,  5,   1.5,  0,    5,   130,  0,    50,   160, 12, 0.5, 0.1, 8),
(NULL, 'سالاد شیرازی 100g',      20,   1,    4,   0,    1.5,  2.5, 15,   0.4,  3,    90,  80, 15, 13,  0,   0),
(NULL, 'قورمه سبزی 100g',        180,  12,   8,   11,   3,    2,   60,   2.5,  350,  300, 200,8,   0.2, 35),
(NULL, 'کباب کوبیده 100g',       250,  20,   5,   16,   0.5,  1,   20,   2.8,  300,  280, 0,  2,   0,   70),
(NULL, 'سینه مرغ کبابی 100g',    165,  31,   0,   3.6,  0,    0,   15,   0.7,  74,   256, 21, 0,   0.2, 85),
(NULL, 'سیب 100g',               52,   0.3,  14,  0.2,  2.4,  10,  6,    0.1,  1,    107, 3,  4.6, 0,   0),
(NULL, 'موز 100g',               89,   1.1,  23,  0.3,  2.6,  12,  5,    0.3,  1,    358, 3,  8.7, 0,   0),
(NULL, 'شیر 1 لیوان 250ml',     150,  8,    12,  8,    0,    12,  293,  0,    105,  366, 68, 0,   3.1, 24),
(NULL, 'عدسی 100g',              116,  9,    20,  0.4,  7.9,  1.8, 19,   3.3,  2,    369, 0,  0,   0,   0),
(NULL, 'سبزیجات پخته 100g',      40,   3,    6,   0.5,  3,    2,   50,   1.5,  60,   200, 100,15,  0,   0),
(NULL, 'پنیر 100g',              264,  25,   2,   17,   0,    0.5, 721,  0.7,  1050, 80,  175,0,   0.5, 70),
(NULL, 'کره 100g',               717,  0.9,  0.1, 81,   0,    0.1, 24,   0,    11,   24,  684,0,   0,   215),
(NULL, 'روغن زیتون 100g',        884,  0,    0,   100,  0,    0,   1,    0.6,  2,    2,   1,   0,  0,   0),
(NULL, 'خیار 100g',              16,   0.7,  3.6, 0.1,  0.5,  1.7, 16,   0.3,  2,    147, 7,  5,   2.8, 0),
(NULL, 'گوجه‌فرنگی 100g',        18,   0.9,  3.9, 0.2,  1.2,  2.6, 10,   0.3,  5,    237, 42, 14,  0,   0),
(NULL, 'پیاز 100g',              40,   1.1,  9.3, 0.1,  1.7,  4.2, 23,   0.2,  4,    146, 0,  0,   7.4, 0),
(NULL, 'سیب‌زمینی 100g',         77,   2,    17,  0.1,  2.2,  0.8, 12,   0.8,  6,    421, 0,  19.7,0,   0),
(NULL, 'گوشت قرمز 100g',         250,  26,   0,   15,   0,    0,   18,   2.6,  72,   340, 0,  0,   0.5, 90),
(NULL, 'ماهی سالمون 100g',       208,  20,   0,   13,   0,    0,   12,   0.8,  59,   363, 0,  0,   11,  55),
(NULL, 'عسل 100g',               304,  0.3,  82,  0,    0.2,  82,  6,    0.4,  4,    14,  0,  0,   0,   0),
(NULL, 'خرما 100g',              277,  1.8,  75,  0.2,  6.7,  66,  64,   0.9,  1,    656, 5,  14,  0,   0),
(NULL, 'کینوا 100g',             120,  4.4,  21,  1.9,  2.8,  0.9, 17,   1.5,  5,    318, 0,  1,   0,   0),
(NULL, 'جو دوسر 100g',           389,  17,   66,  7,    11,   1,   54,   4.7,  2,    362, 0,  0,   0,   0),
(NULL, 'آجیل مخلوط 100g',       607,  20,   21,  54,   7,    4,   105,  3.5,  5,    600, 0,  0,   0,   0);

SET FOREIGN_KEY_CHECKS = 1;
