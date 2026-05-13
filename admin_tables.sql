CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `action` VARCHAR(255) NOT NULL,
  `detail` TEXT DEFAULT NULL,
  `admin_user` VARCHAR(100) DEFAULT 'admin',
  `ip` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `position` VARCHAR(100) NOT NULL,
  `code` TEXT NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `start_date` DATE DEFAULT NULL,
  `end_date` DATE DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `news_id` INT NOT NULL,
  `user_id` INT DEFAULT NULL,
  `author_name` VARCHAR(255) NOT NULL,
  `author_email` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `status` ENUM('pending','approved','rejected','spam') NOT NULL DEFAULT 'pending',
  `ip` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`news_id`) REFERENCES `news`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(500) NOT NULL,
  `original_name` VARCHAR(500) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `size` INT NOT NULL DEFAULT 0,
  `path` VARCHAR(500) NOT NULL,
  `uploaded_by` VARCHAR(100) DEFAULT 'admin',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `news` ADD COLUMN IF NOT EXISTS `author` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `news` ADD COLUMN IF NOT EXISTS `tags` TEXT DEFAULT NULL;
ALTER TABLE `news` ADD COLUMN IF NOT EXISTS `source` VARCHAR(500) DEFAULT NULL;
ALTER TABLE `news` ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT NULL;

INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
('site_title', 'Haber Sitesi'),
('site_desc', 'En güncel haberler, son dakika gelişmeleri ve daha fazlası.'),
('site_keywords', 'haber, son dakika, gündem, spor, ekonomi'),
('site_short_name', 'Haber Sitesi'),
('comments_status', 'open'),
('comment_approval', 'manual'),
('comment_membership', 'all'),
('ads_txt', ''),
('timezone', 'Europe/Istanbul'),
('site_lang', 'tr'),
('rss_enabled', '1'),
('rss_content', '1'),
('mod_weather', '1'),
('mod_prayer', '1'),
('mod_pharmacy', '1'),
('mod_archive', '1'),
('mod_newspaper', '1'),
('mod_daily', '1'),
('mod_membership', '1'),
('seo_news_title', '{kelime} haberleri'),
('seo_news_desc', 'En güncel {kelime} haberleri'),
('seo_archive_title', 'Arşiv'),
('seo_archive_desc', 'Haber arşivi'),
('google_analytics', ''),
('google_adsense', ''),
('facebook_pixel', ''),
('social_facebook', ''),
('social_twitter', ''),
('social_instagram', ''),
('social_youtube', ''),
('censored_words', 'salak,aptal,gerizekalı'),
('comment_info', 'Kanunlara aykırı yorumlar onaylanmamaktadır.');


-- Eksik kolon ekleme (varsa hata vermez)
ALTER TABLE news ADD COLUMN IF NOT EXISTS author VARCHAR(200) DEFAULT '';
ALTER TABLE news ADD COLUMN IF NOT EXISTS tags TEXT DEFAULT NULL;
ALTER TABLE news ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL;
ALTER TABLE news ADD COLUMN IF NOT EXISTS source_url VARCHAR(500) DEFAULT NULL;
ALTER TABLE news ADD COLUMN IF NOT EXISTS show_home TINYINT(1) DEFAULT 1;
ALTER TABLE news ADD COLUMN IF NOT EXISTS comments_on TINYINT(1) DEFAULT 1;
