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

INSERT INTO `articles` (`article_id`, `subject`, `title`, `slug`, `audio_url`, `author`, `source`, `level`, `accent`, `total_duration`, `resource_type`, `word_count`) VALUES
 
(1, 'Civil Engineering',
 'Building the Impossible: Golden Gate Bridge',
 'building-the-impossible-golden-gate-bridge',
 'https://voa-audio.voanews.eu/vle/2025/03/04/77b3e71e-188e-45b7-8f60-08dd5b02d8d7.mp3',
 'Alex Gendler', 'TED-Ed', 'Easy', 'US', 105, 'audio', 490),
 
(2, 'Computer Science',
 'The Birth of the Computer',
 'the-birth-of-the-computer',
 'https://voa-audio.voanews.eu/vle/2025/03/04/77b3e71e-188e-45b7-8f60-08dd5b02d8d7.mp3',
 'George Dyson', 'TED', 'Intermediate', 'US', 105, 'audio', 520),
 
(3, 'Mechanical Engineering with Transportation',
 'The Ethical Dilemma of Self-Driving Cars',
 'ethical-dilemma-self-driving-cars',
 'https://voa-audio.voanews.eu/vle/2025/03/04/77b3e71e-188e-45b7-8f60-08dd5b02d8d7.mp3',
 'Patrick Lin', 'TED-Ed', 'Advanced', 'US', 125, 'audio', 470);
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

INSERT INTO `article_segments`
(`segment_id`, `article_id`, `paragraph_index`, `sentence_index`, `content_en`, `content_cn`, `start_time`, `end_time`) VALUES
 
-- ---------------------------------------------------------
-- Article 1: Golden Gate Bridge (Easy)
-- ---------------------------------------------------------
(1, 1, 1, 0,
 'In the mid-1930s, two familiar spires towered above the morning fog. Stretching 227 meters into the sky, these 22,000-ton towers would help support California''s Golden Gate Bridge.',
 '20世纪30年代中期，两座熟悉的尖塔矗立在晨雾之上。这两座高达227米、重达22000吨的塔架将支撑起加利福尼亚州的金门大桥。',
 0, 18),
 
(2, 1, 2, 0,
 'But since they were currently in Pennsylvania, they first had to be dismantled, packaged, and shipped piece by piece over 4,500 kilometers away. Moving the bridge''s towers across a continent was just one of the challenges facing the project''s lead engineers, Charles Ellis and Joseph Strauss.',
 '但由于这些塔架当时还在宾夕法尼亚州，它们需要先被拆解、打包，然后逐件运送到4500公里之外。将桥塔运送到另一个大陆只是项目首席工程师查尔斯·埃利斯和约瑟夫·施特劳斯面临的众多挑战之一。',
 18, 38),
 
(3, 1, 3, 0,
 'Even before construction began, the pair faced all kinds of opposition. The military feared the bridge would make the important harbor an even more vulnerable target. Ferry companies claimed the bridge would steal their business, and residents wanted to preserve the area''s natural scenery.',
 '甚至在施工开始之前，两人就面临了各种反对意见。军方担心这座桥会使重要的港口成为更容易被攻击的目标。渡轮公司声称这座桥会抢走他们的生意，而当地居民则希望保护该地区的自然风光。',
 38, 60),
 
(4, 1, 4, 0,
 'Worse still, many engineers thought the project was impossible. The Golden Gate Strait was home to 96-kilometer-per-hour winds, swirling tides, an endless blanket of fog, and the earthquake-prone San Andreas fault.',
 '更糟糕的是，许多工程师认为这个项目根本不可能实现。金门海峡有着时速96公里的大风、汹涌的潮汐、无尽的浓雾，以及地震频发的圣安德烈亚斯断层。',
 60, 80),
 
(5, 1, 5, 0,
 'Despite these challenges, the bridge was completed in 1937 and quickly became one of the most recognizable structures in the world. Today, the Golden Gate Bridge stands as a testament to human ingenuity and the power of engineering to overcome seemingly impossible obstacles.',
 '尽管面临这些挑战，这座桥还是在1937年竣工，并迅速成为世界上最具辨识度的建筑之一。如今，金门大桥见证了人类的聪明才智和工程技术克服看似不可能的障碍的力量。',
 80, 105),
 
-- ---------------------------------------------------------
-- Article 2: Birth of the Computer (Intermediate)
-- ---------------------------------------------------------
(6, 2, 1, 0,
 'In 1945, a group of engineers gathered in a small building in Princeton, New Jersey. Their mission was to build one of the first modern computers, a machine that would transform the twentieth century in ways none of them could have fully imagined.',
 '1945年，一群工程师聚集在新泽西州普林斯顿的一栋小楼里。他们的使命是建造第一批现代计算机之一——一台将以他们谁也无法完全想象的方式改变二十世纪的机器。',
 0, 20),
 
(7, 2, 2, 0,
 'The project was led by mathematician John von Neumann, who proposed a revolutionary architecture in which a computer''s program would be stored in the same memory as its data. This concept, now known as the von Neumann architecture, remains the foundation of virtually every computer in use today.',
 '这个项目由数学家约翰·冯·诺伊曼领导，他提出了一种革命性的架构，即计算机的程序将与数据存储在同一内存中。这个概念现在被称为冯·诺伊曼架构，至今仍是几乎所有计算机的基础。',
 20, 48),
 
(8, 2, 3, 0,
 'The engineers faced enormous technical challenges. Memory was one of the most difficult problems. They experimented with cathode ray tubes, using the persistence of phosphor dots on a screen to store binary digits. Each tube could hold about 1,024 bits of information, which was remarkable for the time.',
 '工程师们面临着巨大的技术挑战。内存是最困难的问题之一。他们尝试使用阴极射线管，利用屏幕上磷光点的余辉来存储二进制数字。每根管子可以存储大约1024比特的信息，这在当时是了不起的成就。',
 48, 78),
 
(9, 2, 4, 0,
 'What emerged from that small building in Princeton was far more than a calculating machine. It was the seed of the digital universe we inhabit today. The principles established there led directly to the development of personal computers, the internet, smartphones, and artificial intelligence.',
 '从普林斯顿那栋小楼里诞生的远不止是一台计算机器。它是我们今天所生活的数字世界的种子。在那里确立的原则直接推动了个人电脑、互联网、智能手机和人工智能的发展。',
 78, 105),
 
-- ---------------------------------------------------------
-- Article 3: Self-Driving Cars Ethics (Advanced)
-- ---------------------------------------------------------
(10, 3, 1, 0,
 'Self-driving cars are already cruising the streets today. And while these autonomous vehicles will ultimately be safer and cleaner than their manual counterparts, they cannot completely avoid accidents altogether. How should a self-driving car be programmed if it encounters an unavoidable accident?',
 '自动驾驶汽车如今已经在街道上行驶。虽然这些自动驾驶车辆最终将比人工驾驶的汽车更安全、更环保，但它们无法完全避免所有事故。如果自动驾驶汽车遇到不可避免的事故，应该如何编程？',
 0, 22),
 
(11, 3, 2, 0,
 'This is a modern version of a classic ethical dilemma known as the trolley problem. Imagine a scenario where the car must choose between two harmful outcomes: swerving to avoid a group of pedestrians but hitting a single bystander, or staying on course and striking the group.',
 '这是一个经典伦理困境的现代版本，即著名的"电车问题"。想象一个场景：汽车必须在两个有害的结果之间做出选择——转向以避开一群行人但撞上一个旁观者，还是保持原来的方向撞上那群人。',
 22, 45),
 
(12, 3, 3, 0,
 'Should the car be programmed to minimize the number of casualties, even if it means sacrificing its own passenger? What if the pedestrians are jaywalking and therefore breaking the law? Does that change the moral calculus? Different ethical frameworks give very different answers to these questions.',
 '汽车是否应该被编程为尽量减少伤亡人数，即使这意味着牺牲自己的乘客？如果行人正在违法横穿马路呢？这是否改变了道德计算？不同的伦理框架对这些问题给出了截然不同的答案。',
 45, 70),
 
(13, 3, 4, 0,
 'A utilitarian approach would suggest programming the car to minimize total harm. A deontological perspective might argue that the car should never be used as a weapon against its own passenger, regardless of the consequences. Meanwhile, virtue ethics might focus on what a responsible manufacturer would do.',
 '功利主义的方法建议将汽车编程为最大限度地减少总体伤害。义务论的观点可能认为，无论后果如何，汽车都不应被用作攻击自身乘客的武器。同时，德性伦理学可能关注的是一个负责任的制造商会怎么做。',
 70, 98),
 
(14, 3, 5, 0,
 'These philosophical debates are not merely academic exercises. As autonomous vehicles become more prevalent, manufacturers and regulators will need to make concrete decisions about how these machines handle life-and-death situations. The answers will shape the future of transportation and the relationship between humans and the machines they create.',
 '这些哲学辩论不仅仅是学术练习。随着自动驾驶汽车越来越普及，制造商和监管机构将需要就这些机器如何处理生死攸关的情况做出具体决策。答案将塑造交通运输的未来，以及人类与他们所创造的机器之间的关系。',
 98, 125);
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

INSERT INTO `tags` (`tag_id`, `name`, `slug`) VALUES
(1, 'Bridge Engineering',      'bridge-engineering'),
(2, 'Structural Design',       'structural-design'),
(3, 'Construction',            'construction'),
(4, 'Artificial Intelligence', 'artificial-intelligence'),
(5, 'Computer Science',        'computer-science'),
(6, 'Innovation',              'innovation'),
(7, 'Autonomous Vehicles',     'autonomous-vehicles'),
(8, 'Transportation Systems',  'transportation-systems'),
(9, 'History of Engineering',  'history-of-engineering'),
(10, 'Ethics in Technology',   'ethics-in-technology');
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

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 9),
(2, 4), (2, 5), (2, 6), (2, 9),
(3, 7), (3, 8), (3, 10), (3, 4);
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

INSERT INTO `users` (`user_id`, `name`, `email`, `password`) VALUES
(1, 'Alice Zhang',   'alice@example.com',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'Bob Chen',      'bob@example.com',     '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'Carol Liu',     'carol@example.com',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
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
