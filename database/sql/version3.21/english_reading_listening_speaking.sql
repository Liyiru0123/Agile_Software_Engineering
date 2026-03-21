-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema english_reading
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `english_reading` ;

-- -----------------------------------------------------
-- Schema english_reading
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `english_reading` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `english_reading` ;

-- -----------------------------------------------------
-- Table `english_reading`.`articles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`articles` (
  `article_id` INT NOT NULL AUTO_INCREMENT,
  `subject` ENUM('Civil Engineering', 'Mathematics', 'Computer Science', 'Mechanical Engineering', 'Mechanical Engineering with Transportation') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `audio_url` VARCHAR(255) NULL DEFAULT NULL COMMENT '完整音频地址',
  `video_url` VARCHAR(255) NULL DEFAULT NULL COMMENT '视频资源地址(可选)',
  `author` VARCHAR(100) NULL DEFAULT NULL,
  `source` VARCHAR(255) NULL DEFAULT NULL,
  `level` ENUM('Easy', 'Intermediate', 'Advanced') NOT NULL,
  `accent` ENUM('US', 'UK') NULL DEFAULT 'US' COMMENT '英美音分类',
  `total_duration` INT NULL DEFAULT '0' COMMENT '音频总时长(秒)',
  `resource_type` ENUM('text', 'audio', 'video') NULL DEFAULT 'text' COMMENT '资源主要类型',
  `word_count` INT NOT NULL DEFAULT '0',
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`),
  UNIQUE INDEX `slug` (`slug` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`article_segments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`article_segments` (
  `segment_id` INT NOT NULL AUTO_INCREMENT,
  `article_id` INT NOT NULL,
  `paragraph_index` INT NOT NULL COMMENT '段落序号',
  `sentence_index` INT NOT NULL COMMENT '句子在段落中的序号',
  `content_en` TEXT NOT NULL COMMENT '英文原文',
  `content_cn` TEXT NULL DEFAULT NULL COMMENT '中文翻译',
  `start_time` INT NOT NULL COMMENT '句子在音频中的开始时间',
  `end_time` INT NOT NULL COMMENT '句子在音频中的结束时间',
  PRIMARY KEY (`segment_id`),
  INDEX `idx_article_segment` (`article_id` ASC, `paragraph_index` ASC) VISIBLE,
  CONSTRAINT `fk_segment_article`
    FOREIGN KEY (`article_id`)
    REFERENCES `english_reading`.`articles` (`article_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`tags` (
  `tag_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(60) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tag_id`),
  UNIQUE INDEX `slug` (`slug` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`article_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`article_tags` (
  `article_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`, `tag_id`),
  UNIQUE INDEX `unique_article_tag` (`article_id` ASC, `tag_id` ASC) VISIBLE,
  INDEX `tag_id` (`tag_id` ASC) VISIBLE,
  CONSTRAINT `article_tags_ibfk_1`
    FOREIGN KEY (`article_id`)
    REFERENCES `english_reading`.`articles` (`article_id`)
    ON DELETE CASCADE,
  CONSTRAINT `article_tags_ibfk_2`
    FOREIGN KEY (`tag_id`)
    REFERENCES `english_reading`.`tags` (`tag_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `email` (`email` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`favorites`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`favorites` (
  `user_id` INT NOT NULL,
  `article_id` INT NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `article_id`),
  UNIQUE INDEX `unique_user_favorite` (`user_id` ASC, `article_id` ASC) VISIBLE,
  INDEX `article_id` (`article_id` ASC) VISIBLE,
  CONSTRAINT `favorites_ibfk_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`)
    ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2`
    FOREIGN KEY (`article_id`)
    REFERENCES `english_reading`.`articles` (`article_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`learning_statistics`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`learning_statistics` (
  `stat_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `stat_date` DATE NOT NULL,
  `listening_minutes` INT NULL DEFAULT '0',
  `speaking_minutes` INT NULL DEFAULT '0',
  `reading_minutes` INT NULL DEFAULT '0',
  PRIMARY KEY (`stat_id`),
  UNIQUE INDEX `unique_user_date` (`user_id` ASC, `stat_date` ASC) VISIBLE,
  CONSTRAINT `fk_stat_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`listening_progress`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`listening_progress` (
  `user_id` INT NOT NULL,
  `article_id` INT NOT NULL,
  `last_position` INT NULL DEFAULT '0' COMMENT '上次播放位置(秒)',
  `playback_speed` FLOAT NULL DEFAULT '1' COMMENT '用户倍速偏好(0.5, 1.0, 1.5, 2.0)',
  `is_completed` TINYINT(1) NULL DEFAULT '0' COMMENT '是否听完',
  `listen_count` INT NULL DEFAULT '0' COMMENT '循环播放次数',
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `article_id`),
  INDEX `fk_listen_article` (`article_id` ASC) VISIBLE,
  CONSTRAINT `fk_listen_article`
    FOREIGN KEY (`article_id`)
    REFERENCES `english_reading`.`articles` (`article_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_listen_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`questions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`questions` (
  `question_id` INT NOT NULL AUTO_INCREMENT,
  `article_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `options` JSON NOT NULL COMMENT '‘\'JSON format：{\"A\":\"option content \",\"B\":\"option content\"...}\'',
  `answer` VARCHAR(10) NOT NULL,
  `explanation` TEXT NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`),
  INDEX `article_id` (`article_id` ASC) VISIBLE,
  CONSTRAINT `questions_ibfk_1`
    FOREIGN KEY (`article_id`)
    REFERENCES `english_reading`.`articles` (`article_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`question_attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`question_attempts` (
  `user_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `user_answer` VARCHAR(10) NOT NULL,
  `is_correct` TINYINT(1) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `question_id`),
  INDEX `user_id` (`user_id` ASC) VISIBLE,
  INDEX `question_id` (`question_id` ASC) VISIBLE,
  CONSTRAINT `question_attempts_ibfk_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`)
    ON DELETE CASCADE,
  CONSTRAINT `question_attempts_ibfk_2`
    FOREIGN KEY (`question_id`)
    REFERENCES `english_reading`.`questions` (`question_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`reading_history`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`reading_history` (
  `user_id` INT NOT NULL,
  `article_id` INT NOT NULL,
  `is_completed` TINYINT NULL DEFAULT 1,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `article_id`),
  INDEX `user_id` (`user_id` ASC) VISIBLE,
  INDEX `article_id` (`article_id` ASC) VISIBLE,
  CONSTRAINT `reading_history_ibfk_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`)
    ON DELETE CASCADE,
  CONSTRAINT `reading_history_ibfk_2`
    FOREIGN KEY (`article_id`)
    REFERENCES `english_reading`.`articles` (`article_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`speaking_attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`speaking_attempts` (
  `user_id` INT NOT NULL,
  `segment_id` INT NOT NULL COMMENT '关联具体的句子',
  `record_url` VARCHAR(255) NOT NULL COMMENT '用户录音存储在云端的URL',
  `overall_score` DECIMAL(5,2) NULL DEFAULT NULL COMMENT '综合得分(0-100)',
  `accuracy_score` DECIMAL(5,2) NULL DEFAULT NULL COMMENT '发音准度得分',
  `fluency_score` DECIMAL(5,2) NULL DEFAULT NULL COMMENT '流利度得分',
  `ai_feedback_json` JSON NULL DEFAULT NULL COMMENT '存储AI详细反馈，如哪些单词读错',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `segment_id`),
  INDEX `idx_user_segment` (`user_id` ASC, `segment_id` ASC) VISIBLE,
  INDEX `fk_speak_segment` (`segment_id` ASC) VISIBLE,
  CONSTRAINT `fk_speak_segment`
    FOREIGN KEY (`segment_id`)
    REFERENCES `english_reading`.`article_segments` (`segment_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_speak_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `english_reading`.`vocabulary_notes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `english_reading`.`vocabulary_notes` (
  `vocabulary_note_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `word` VARCHAR(100) NOT NULL,
  `definition` TEXT NOT NULL,
  `example` TEXT NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vocabulary_note_id`),
  INDEX `fk_vocabulary_notes_users1_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_vocabulary_notes_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `english_reading`.`users` (`user_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
