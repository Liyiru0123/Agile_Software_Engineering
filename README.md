# Agile Software Engineering / 敏捷软件工程项目

一个基于 Laravel 12 构建的英语学习与阅读训练平台，围绕“技能训练 + 学习记录 + 社区互动”形成完整学习闭环。  
An English learning and reading-training platform built with Laravel 12, covering skill training, learning records, and community interaction.

---

## 1. 项目介绍 / Project Overview

### 背景 / Background
本项目面向英语学习场景，目标是构建一个可运行、可扩展、可协作开发的学习平台。系统支持技能练习、收藏、生词本、学习分析、论坛社区、好友消息与商店系统。  
This project targets English learning scenarios and aims to build a runnable, extensible, and collaborative platform. It supports skill training, favorites, vocabulary notebook, learning analytics, forum community, messaging, and a shop system.

---

## 2. 技术架构 / Technical Architecture

### 总体架构 / Overall Architecture
本项目采用 **Laravel 单体应用架构**，前后端统一在一个仓库中开发和部署。  
This project uses a **Laravel monolithic architecture**, with frontend and backend developed in one repository.

- 展示层 / Presentation: Blade 模板 + JavaScript
- 控制层 / Controller: Laravel Controllers
- 业务层 / Service: Services 封装训练、翻译、语音评价等逻辑
- 数据层 / Data: Eloquent Models + MySQL / SQLite
- 构建层 / Build: Vite
- 测试交付 / Testing & Delivery: PHPUnit + GitHub Actions

### 技术栈 / Tech Stack

**后端 / Backend**
- PHP 8.2+
- Laravel 12
- Eloquent ORM
- PHPUnit 11

**前端 / Frontend**
- Blade Templates
- Vite 7
- Tailwind CSS 4
- Axios
- JavaScript

**数据库 / Database**
- MySQL（开发/部署推荐） / Recommended for development and deployment
- SQLite（测试/CI 推荐） / Recommended for testing and CI

### 目录结构 / Directory Structure

```text
app/
  Http/Controllers/
  Models/
  Services/
database/
  migrations/
  seeders/
resources/
  views/
routes/
  web.php
  api.php
tests/
  Feature/
  Unit/
.github/workflows/
```

### 核心模块 / Core Modules
- 认证 / Authentication
- 听说训练 / Listening, speaking
- 学习数据 / Learning data
- 社区与社交 / Community and social
- 商店系统 / Shop system
- Wordle 小游戏 / Wordle mini-game

---

## 3. 功能清单 / Feature List

### 用户与认证 / User & Authentication
- 用户注册、登录、登出 / Register, login, logout
- 页面鉴权保护 / Auth-protected pages

### 首页与学习总览 / Dashboard
- 今日任务与学习计划 / Daily tasks and study plans
- 周/月统计摘要 / Weekly and monthly summaries
- 收藏、历史、生词本、积分管理、学习分析、社区摘要 / Favorites, history, notebook, point management, study analysis and community summaries

### 文章 / Articles 
- 文章列表与详情页 / Article list and detail pages
- 关键词搜索 / Keyword search
- 技能、难度、收藏、进度筛选 / Filters by skill, difficulty, favorites, and progress
- 阅读历史记录 / Reading history tracking

### 技能训练 / Skill Training
- Listening：听力训练与结果保存 / listening practice and result saving
- Speaking：录音、shadowing 跟读、AI 评分 / recording, shadowing, and AI scoring

### 学习辅助 / Learning Support
- 划词翻译 / Text selection translation
- 生词本 / Vocabulary notebook
- 收藏文章 / Favorite articles
- 从收藏生成学习计划 / Generate plans from favorites
- 学习分析页 / Learning analytics page

### 社区与社交 / Community & Social
- Forum 发帖、评论、标签 / forum posts, comments, and tags
- 点赞与收藏帖子 / like and save posts
- 好友申请与私信 / friend requests and private messaging

### 其他功能 / Extra Features
- 商店系统 / Shop system
- Wordle 小游戏 / Wordle mini-game

---

## 4. 环境要求 / Environment Requirements

请确保已安装以下环境：  
Please make sure the following are installed:

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18
- npm >= 9
- MySQL >= 8.0
- Git

推荐开发环境 / Recommended:
- Windows 10/11, macOS, Linux
- VS Code / Cursor / PhpStorm
- Chrome / Edge

推荐 PHP 扩展 / Recommended PHP extensions:
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo

---

## 5. 部署步骤 / Deployment Guide

### 5.1 克隆项目 / Clone

```bash
git clone https://github.com/Liyiru0123/Agile_Software_Engineering.git
cd Agile_Software_Engineering
```

### 5.2 安装依赖 / Install Dependencies

```bash
composer install
npm install
```

### 5.3 初始化环境 / Initialize Environment

Windows:

```bash
copy .env.example .env
php artisan key:generate
```

macOS / Linux:

```bash
cp .env.example .env
php artisan key:generate
```

### 5.4 配置数据库 / Configure Database
编辑 `.env` / Edit `.env`:

```env
APP_NAME="Agile Software Engineering"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning
DB_USERNAME=root
DB_PASSWORD=
```

### 5.5 迁移并填充数据 / Migrate and Seed

```bash
php artisan migrate:fresh --seed
```

该命令会创建表并导入文章、训练、词汇与演示数据。  
This command creates tables and imports article, training, vocabulary, and demo data.

### 5.6 启动项目 / Start the App

方式一 / Option 1:

```bash
php artisan serve
npm run dev
```

方式二 / Option 2:

```bash
composer run dev
```

访问 / Visit:

```text
http://127.0.0.1:8000
```

### 5.7 生产部署建议 / Production Tips

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

并确保 `storage/` 与 `bootstrap/cache/` 可写，Web 服务器指向 `public/`。  
Make sure `storage/` and `bootstrap/cache/` are writable and the web server points to `public/`.

---

## 6. 测试方法 / Testing

### 本地测试 / Local Testing

```bash
composer test
```

或 / or

```bash
php artisan test
```

---

## 7. 团队分工 / Team Responsibilities

### 团队名称 / Team Name**TeamSpirit**
### 成员 / Members

| Nickname          | NAME         |
| ----------------- | ------------ |
| Sword_Holder      | Jiarui Zheng |
| MidsummerFantasia | Wentong Yang |
| Phrxey            | Zijian Cao   |
| Superb            | Jianyuan Gui |
| FDG-ANSWER        | Sihan Huang  |
| Liyiru0123        | Yiru Li      |
| Zzx0112-zeri      | Zhixin Zhu   |
| jiojioYize        | Yize Xiao    |

---

## 8. 快速开始 / Quick Start

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

打开 / Open:

```text
http://127.0.0.1:8000
```

运行测试 / Run tests:

```bash
composer test
```
