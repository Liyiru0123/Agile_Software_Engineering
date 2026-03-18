-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2026-03-18 22:28:05
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `english_reading`
--

-- --------------------------------------------------------

--
-- 表的结构 `articles`
--

CREATE TABLE `articles` (
  `article_id` int(11) NOT NULL,
  `subject` enum('Civil Engineering','Mathematics','Computer Science','Mechanical Engineering','Mechanical Engineering with Transportation') NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `level` enum('Easy','Intermediate','Advanced') NOT NULL,
  `read_count` int(11) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `excerpt` text NOT NULL COMMENT '文章摘要',
  `word_count` int(11) NOT NULL DEFAULT 0 COMMENT '单词数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `articles`
--

INSERT INTO `articles` (`article_id`, `subject`, `title`, `slug`, `content`, `author`, `source`, `level`, `read_count`, `deleted_at`, `created_at`, `updated_at`, `excerpt`, `word_count`) VALUES
(1, 'Computer Science', 'The Science of Reading', 'the-science-of-reading', 'Reading is a complex cognitive process that involves multiple areas of the brain. When we read, our brain must recognize letters, decode words, and comprehend meaning. This process becomes more automatic with practice, allowing fluent readers to focus on understanding rather than decoding.', 'Dr. Smith', NULL, 'Intermediate', 156, NULL, '2026-03-17 05:14:03', '2026-03-18 13:10:29', 'Understanding how the brain processes written language', 150),
(2, 'Civil Engineering', 'Daily Life in English Speaking Countries', 'daily-life-in-english-speaking-countries', 'In English-speaking countries like the US, UK, and Australia, daily life has both similarities and differences. Breakfast is typically light, with coffee or tea, toast, and cereal. Work hours are usually 9 to 5, with a one-hour lunch break. Weekends are often spent with family or pursuing hobbies.', 'Jane Doe', NULL, 'Easy', 98, NULL, '2026-03-17 05:14:03', '2026-03-18 13:21:23', 'Cultural norms and daily routines in English-speaking nations', 200),
(3, 'Mathematics', 'Shakespeare and Modern English', 'shakespeare-and-modern-english', 'William Shakespeare is widely regarded as the greatest writer in the English language. He coined over 1700 words and phrases that are still in use today, such as \"break the ice\", \"heart of gold\", and \"wild goose chase\". His plays continue to be performed and studied worldwide, making him a lasting influence on English literature and language.', 'Prof. Brown', NULL, 'Advanced', 120, NULL, '2026-03-17 05:14:03', '2026-03-18 10:51:12', 'How Shakespeare influenced the English language we use today', 250),
(7, 'Mechanical Engineering', 'test', 'test-69badc78cfbfe', 'test', 'admin', NULL, 'Easy', 10, NULL, '2026-03-18 09:10:16', '2026-03-18 13:03:11', 'test', 1);

-- --------------------------------------------------------

--
-- 表的结构 `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `favorites`
--

INSERT INTO `favorites` (`user_id`, `article_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(7, 2, NULL, '2026-03-18 02:54:14', '2026-03-18 02:54:14'),
(8, 7, NULL, '2026-03-18 13:19:07', '2026-03-18 13:19:07');

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_03_17_070546_create_sessions_table', 1),
(2, '2026_03_17_125424_add_is_admin_to_users_table', 2);

-- --------------------------------------------------------

--
-- 表的结构 `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL COMMENT '关联文章ID',
  `content` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '‘''JSON format：{"A":"option content ","B":"option content"...}''' CHECK (json_valid(`options`)),
  `answer` varchar(10) NOT NULL,
  `type` enum('single','multiple') NOT NULL DEFAULT 'single' COMMENT '题目类型：single=单选，multiple=多选',
  `explanation` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `questions`
--

INSERT INTO `questions` (`question_id`, `article_id`, `content`, `options`, `answer`, `type`, `explanation`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 7, 'a', '{\"A\":\"a\",\"B\":\"a\",\"C\":\"a\",\"D\":\"a\"}', '[\"A\"]', 'single', 'a', NULL, '2026-03-18 11:01:42', '2026-03-18 11:01:42');

-- --------------------------------------------------------

--
-- 表的结构 `question_attempts`
--

CREATE TABLE `question_attempts` (
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_answer` varchar(10) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `is_added_wrong` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否加入错题本：0=否，1=是',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `question_attempts`
--

INSERT INTO `question_attempts` (`user_id`, `question_id`, `user_answer`, `is_correct`, `is_added_wrong`, `created_at`, `updated_at`) VALUES
(8, 2, '\"[\\\"A\\\"]\"', 1, 0, '2026-03-18 13:03:15', '2026-03-18 13:03:15');

-- --------------------------------------------------------

--
-- 表的结构 `reading_history`
--

CREATE TABLE `reading_history` (
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `progress` int(11) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `reading_history`
--

INSERT INTO `reading_history` (`user_id`, `article_id`, `progress`, `read_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(8, 1, 0, '2026-03-18 13:10:30', NULL, '2026-03-18 13:10:30', '2026-03-18 13:10:30'),
(8, 2, 0, '2026-03-18 13:21:24', NULL, '2026-03-18 13:21:24', '2026-03-18 13:21:24'),
(8, 3, 0, '2026-03-18 10:51:13', NULL, '2026-03-18 10:51:13', '2026-03-18 10:51:13'),
(8, 7, 0, '2026-03-18 13:03:12', NULL, '2026-03-18 13:03:12', '2026-03-18 13:03:12');

-- --------------------------------------------------------

--
-- 表的结构 `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('rIfyfr1wJNkJIArBKqxZBAhqnAEJxdoUsttjzeWb', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUExpYVJ5NERFUDgyR0VCdGxtSllySGFsT0lQbUZxVmhHY05kSVowaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1773869220);

-- --------------------------------------------------------

--
-- 表的结构 `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_admin` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否管理员（0=否，1=是）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `deleted_at`, `created_at`, `updated_at`, `is_admin`) VALUES
(6, '普通用户', 'user@example.com', '$2y$12$rh0svOhOvw.ve5ivW3rV4u7ltpJ0azr2dJ.SJ39sHZmNIZscXP31K', NULL, '2026-03-17 05:14:03', '2026-03-17 05:14:03', 0),
(7, 'yuan', 'yuan@123', '$2y$12$Y5H.Cp5mEh7t6W05g0XfPOVXtOlzh.Iwtt4R90QgVITeifHVlWU8a', NULL, '2026-03-17 08:20:42', '2026-03-17 08:20:42', 0),
(8, 'admin', 'admin@123', '$2y$12$fgBXB4pIBVOQ5i61Y1f5JuCCvTaQCRZtjFoei7CTaDxJAcfQs53V.', NULL, '2026-03-17 18:24:18', '2026-03-18 02:24:56', 1);

-- --------------------------------------------------------

--
-- 表的结构 `vocabulary_notes`
--

CREATE TABLE `vocabulary_notes` (
  `vocabulary_note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL,
  `definition` text NOT NULL,
  `example` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `wrong_questions`
--

CREATE TABLE `wrong_questions` (
  `wrong_question_id` int(11) NOT NULL COMMENT '错题ID（主键）',
  `user_id` int(11) NOT NULL COMMENT '关联用户ID（匹配 users.user_id 类型）',
  `question_id` int(11) NOT NULL COMMENT '关联题目ID（匹配 questions.question_id 类型）',
  `user_answer` text DEFAULT NULL COMMENT '用户答题时的答案',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT '加入时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '软删除标记'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户错题本表';

--
-- 转储表的索引
--

--
-- 表的索引 `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- 表的索引 `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD UNIQUE KEY `unique_article_tag` (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- 表的索引 `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`article_id`),
  ADD UNIQUE KEY `unique_user_favorite` (`user_id`,`article_id`),
  ADD KEY `article_id` (`article_id`);

--
-- 表的索引 `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `article_id` (`article_id`);

--
-- 表的索引 `question_attempts`
--
ALTER TABLE `question_attempts`
  ADD PRIMARY KEY (`user_id`,`question_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `question_id` (`question_id`);

--
-- 表的索引 `reading_history`
--
ALTER TABLE `reading_history`
  ADD PRIMARY KEY (`user_id`,`article_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `article_id` (`article_id`);

--
-- 表的索引 `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- 表的索引 `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 表的索引 `vocabulary_notes`
--
ALTER TABLE `vocabulary_notes`
  ADD PRIMARY KEY (`vocabulary_note_id`),
  ADD KEY `fk_vocabulary_notes_users1_idx` (`user_id`);

--
-- 表的索引 `wrong_questions`
--
ALTER TABLE `wrong_questions`
  ADD PRIMARY KEY (`wrong_question_id`),
  ADD UNIQUE KEY `uk_user_question` (`user_id`,`question_id`) COMMENT '一个用户同一题仅存一次',
  ADD KEY `idx_wrong_questions_deleted_at` (`deleted_at`),
  ADD KEY `fk_wrong_questions_question_id` (`question_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `vocabulary_notes`
--
ALTER TABLE `vocabulary_notes`
  MODIFY `vocabulary_note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `wrong_questions`
--
ALTER TABLE `wrong_questions`
  MODIFY `wrong_question_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '错题ID（主键）', AUTO_INCREMENT=4;

--
-- 限制导出的表
--

--
-- 限制表 `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- 限制表 `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;

--
-- 限制表 `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_article_id` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;

--
-- 限制表 `question_attempts`
--
ALTER TABLE `question_attempts`
  ADD CONSTRAINT `question_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE;

--
-- 限制表 `reading_history`
--
ALTER TABLE `reading_history`
  ADD CONSTRAINT `reading_history_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_history_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;

--
-- 限制表 `vocabulary_notes`
--
ALTER TABLE `vocabulary_notes`
  ADD CONSTRAINT `fk_vocabulary_notes_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- 限制表 `wrong_questions`
--
ALTER TABLE `wrong_questions`
  ADD CONSTRAINT `fk_wrong_questions_question_id` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wrong_questions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
