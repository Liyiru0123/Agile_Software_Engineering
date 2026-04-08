# Agile Software Engineering / 敏捷软件工程项目

一个基于 Laravel 12 构建的英语学习与阅读训练平台，围绕“文章阅读 + 技能训练 + 学习记录 + 社区互动”形成完整学习闭环。  
An English learning and reading-training platform built with Laravel 12, covering article reading, skill training, learning records, and community interaction.

---

## 1. 项目介绍 / Project Overview

### 背景 / Background
本项目面向英语学习场景，目标是构建一个可运行、可扩展、可协作开发的学习平台。系统支持文章浏览、阅读训练、听说读写练习、收藏、生词本、学习分析、论坛社区、好友消息与陪伴系统。  
This project targets English learning scenarios and aims to build a runnable, extensible, and collaborative platform. It supports article browsing, reading practice, four-skill training, favorites, vocabulary notebook, learning analytics, forum community, messaging, and a companion system.

### 目标 / Objectives
- 提供结构清晰的英语学习平台 / Provide a well-structured English learning platform
- 打通“阅读 → 练习 → 记录 → 分析 → 互动”的学习闭环 / Build a full learning loop from reading to practice, records, analytics, and interaction
- 支持 GitHub 协作开发与 CI/CD / Support GitHub collaboration and CI/CD
- 适用于课程项目展示、答辩和后续扩展 / Suitable for course presentation, defense, and future extension

### 使用场景 / Use Cases
- 软件工程课程项目 / Software engineering course project
- Agile / Scrum 团队开发实践 / Agile or Scrum teamwork practice
- Laravel Web 应用开发练习 / Laravel web application practice

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
- 文章与阅读 / Articles and reading
- 听说读写训练 / Listening, speaking, reading, writing
- 学习数据 / Learning data
- 社区与社交 / Community and social
- 陪伴系统 / Companion system
- Wordle 小游戏 / Wordle mini-game

---

## 3. 功能清单 / Feature List

### 用户与认证 / User & Authentication
- 用户注册、登录、登出 / Register, login, logout
- 页面鉴权保护 / Auth-protected pages

### 首页与学习总览 / Dashboard
- 今日任务与学习计划 / Daily tasks and study plans
- 周/月统计摘要 / Weekly and monthly summaries
- 收藏、历史、生词本、社区摘要 / Favorites, history, notebook, and community summaries

### 文章与阅读 / Articles & Reading
- 文章列表与详情页 / Article list and detail pages
- 关键词搜索 / Keyword search
- 技能、难度、收藏、进度筛选 / Filters by skill, difficulty, favorites, and progress
- 阅读历史记录 / Reading history tracking

### 四项训练 / Four Skill Training
- Listening：听力训练与结果保存 / listening practice and result saving
- Reading：阅读理解题与解析 / comprehension questions and explanations
- Speaking：录音、shadowing 跟读、AI 评分 / recording, shadowing, and AI scoring
- Writing：写作任务与反馈 / writing tasks and feedback

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
- Companion 陪伴系统 / Companion system
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

### 测试类型 / Test Types
- 单元测试 / Unit tests
- 功能测试 / Feature tests
- 集成测试 / Integration tests

### 当前覆盖模块 / Covered Areas
- Dashboard 学习计划 / dashboard plans
- Article API / article API
- 划词翻译与保存 / selection translation and save flow
- 阅读历史 / reading history
- 收藏与计划生成 / favorites and plan generation
- 文本处理逻辑 / text processing logic

### CI/CD
项目已配置 GitHub Actions，在 PR 和主分支推送时自动运行测试。  
GitHub Actions is configured to run tests automatically on pull requests and pushes to the main branch.

相关文件 / Related files:
- `.github/workflows/ci.yml`
- `CONTRIBUTING.md`
- `.github/CODEOWNERS`

### 合并要求 / Merge Policy
- 测试必须通过 / Tests must pass
- PR 至少 1 位人工审核 / At least one human approval is required
- AI/Copilot 仅作辅助参考 / AI or Copilot review is advisory only

---

## 7. 团队分工 / Team Responsibilities

### 团队名称 / Team Name
- **TeamSpirit**

### 分组 / Groups
- **UI Team（2人 / 2 members）**：界面设计与交互 / UI design and interaction
- **Database Team（2人 / 2 members）**：数据库设计与数据准备 / database design and seed data
- **Backend Team（4人 / 4 members）**：业务逻辑、路由、接口与训练模块 / business logic, routes, APIs, and training modules

### 成员分工 / Members

**UI Team**
- **Jianyuan Gui**：首页、文章列表页、阅读页设计优化 / home, article list, and reading page design
- **Zijian Cao**：登录/注册、收藏、阅读记录、交互页样式 / auth, favorites, history, and interaction pages

**Database Team**
- **Wentong Yang**：核心数据表设计、迁移、测试数据 / core tables, migrations, and seed data
- **Yiru Li**：数据库主干规范、README、迁移规范 / schema conventions, README, and migration rules

**Backend Team**
- **Yize Xiao**：登录与个人主页逻辑 / authentication and homepage logic
- **Zhixin Zhu**：文章模块、阅读模块、训练入口 / article module, reading module, and training entry
- **Jiarui Zheng**：练习模块、接口联调、结果反馈 / practice module, API integration, result feedback
- **Sihan Huang**：词汇、统计、扩展与整合 / vocabulary, analytics, extensions, and integration

### 协作方式 / Collaboration
- Git + GitHub
- 分支开发 / branch-based development
- Pull Request 审查 / PR reviews
- Scrum / Agile 迭代开发 / Scrum and Agile iteration

---

## 8. 主要路由与模块 / Routes and Modules

### Web 页面 / Web Pages
`routes/web.php` 覆盖：  
`routes/web.php` covers:
- 首页 / Dashboard
- 学习分析 / analytics
- 文章列表与详情 / article list and detail
- 听说读写训练页 / training pages
- 收藏与计划页 / favorites and planning
- 阅读历史 / history
- 生词本 / notebook
- Forum 社区 / forum
- 好友与消息 / friends and messaging
- 陪伴页 / companion
- 登录注册 / auth pages

### API / APIs
`routes/api.php` 提供：  
`routes/api.php` provides:
- `articles`
- `favorites`
- `reading-history`
- `tags`
- `vocabulary-notes`
- 题目获取与提交接口 / question fetch and submission APIs

### Services
- `ArticleTextProcessor`
- `ReadingExerciseService`
- `SpeakingExerciseService`
- `LangblyTranslationService`
- `GeminiAudioService`
- `QwenOmniAudioService`
- `OllamaSpeakingService`
- `CompanionService`

---

## 9. 开发规范 / Development Conventions

### 分支规范 / Branch Strategy
- `main`：稳定版本 / stable
- `dev`：开发主分支 / development
- `feature/xxx`：功能分支 / feature
- `fix/xxx`：修复分支 / fix

### Commit 规范 / Commit Prefixes
- `feat:`
- `fix:`
- `refactor:`
- `test:`
- `docs:`
- `chore:`

### API 返回格式 / API Response Format

```json
{
  "code": 0,
  "message": "success",
  "data": {}
}
```

---

## 10. 项目亮点 / Highlights

- 基于 Laravel 的完整单体式课程项目 / Complete Laravel monolithic course project
- 覆盖听说读写训练、社区、社交与成长系统 / Covers training, community, social, and growth systems
- 具备测试、CI/CD、人工评审流程 / Includes testing, CI/CD, and human review workflow
- 适合课程展示与后续扩展 / Suitable for presentation and future extension

---

## 11. 后续可扩展方向 / Future Improvements

- 管理员后台 / admin panel
- Swagger / API 文档 / API docs
- 更高测试覆盖率 / more test coverage
- 更细粒度权限控制 / finer-grained permissions
- 更完整的数据分析看板 / richer analytics dashboard
- Docker 部署支持 / Docker support

---

## 12. 相关文档 / Related Documents

- `PLAN&PROCESS/Plan.md`
- `INNERDOC/README.md`
- `CONTRIBUTING.md`
- `.github/workflows/ci.yml`

---

## 13. 快速开始 / Quick Start

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
