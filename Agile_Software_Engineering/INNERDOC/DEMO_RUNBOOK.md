# Demo Runbook (v3.21)

## 1. 目的
本说明用于团队成员快速完成本地 Demo 环境准备，确保所有人看到一致的数据与接口行为。

适用版本：当前 main 分支（以最新提交为准）
数据源：database/sql/version3.21/english_reading_listening_speaking.sql

## 2. 最低环境要求
- PHP 8.2+
- Composer
- MySQL 8+
- Node.js + npm
- Windows PowerShell（本文命令以 PowerShell 为例）

## 3. 拉取代码
在项目根目录执行：

```powershell
git pull --ff-only
```

如果分支不是 main：

```powershell
git switch main
git pull --ff-only
```

## 4. 初始化项目依赖
在项目根目录执行：

```powershell
composer install
npm install
```

## 5. 配置环境变量
1. 确认存在 `.env` 文件（没有就复制 `.env.example`）。
2. 核对数据库配置：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_reading
DB_USERNAME=root
DB_PASSWORD=
```

## 6. 导入 Demo 数据
说明：本 SQL 文件包含 `DROP SCHEMA` 和 `CREATE SCHEMA`，会重建 `english_reading` 数据库。

```powershell
mysql -u root -p < "database/sql/version3.21/english_reading_listening_speaking.sql"
```

## 7. Laravel 启动前准备
```powershell
php artisan key:generate
php artisan storage:link
php artisan config:clear
php artisan cache:clear
```

## 8. 启动服务
后端服务：

```powershell
php artisan serve
```

前端资源（可选，但建议）：

```powershell
npm run dev
```

## 9. 导入后快速自检
在 MySQL 中执行以下检查：

```sql
USE english_reading;

SELECT 'articles' AS tbl, COUNT(*) AS cnt FROM articles
UNION ALL
SELECT 'article_segments', COUNT(*) FROM article_segments
UNION ALL
SELECT 'tags', COUNT(*) FROM tags
UNION ALL
SELECT 'article_tags', COUNT(*) FROM article_tags
UNION ALL
SELECT 'users', COUNT(*) FROM users;

SELECT slug, COUNT(*) c FROM articles GROUP BY slug HAVING c > 1;
SELECT slug, COUNT(*) c FROM tags GROUP BY slug HAVING c > 1;
SELECT email, COUNT(*) c FROM users GROUP BY email HAVING c > 1;

SELECT s.segment_id, s.article_id
FROM article_segments s
LEFT JOIN articles a ON a.article_id = s.article_id
WHERE a.article_id IS NULL;
```

预期：
- 第一组统计有记录数
- 唯一性查询返回 0 行
- 外键孤儿查询返回 0 行

## 10. Demo 演示建议顺序
1. 文章列表：展示已有文章基础信息。
2. 阅读结构：展示按段落与句子拆分后的数据。
3. 标签关联：展示文章与 tags 的关联关系。
4. 音频信息：展示 `audio_url`、`accent`、`total_duration`。

建议接口路径：
- GET /api/articles
- GET /api/articles/{id}
- GET /api/articles/{id}/reading
- GET /api/articles/{id}/audio

## 11. 常见问题
1. `SQLSTATE[HY000] [1045] Access denied`
- 检查 `.env` 的 DB 用户名密码。

2. `Class not found` 或依赖报错
- 重新执行 `composer install`。

3. 前端资源 404 或样式丢失
- 执行 `npm install` 后 `npm run dev`。

4. 音频 URL 打不开
- 先确认数据中 `audio_url` 可访问；本 Demo 可先验证字段返回，不要求实际播放成功。

## 12. 交付要求（团队统一）
- 演示前统一同步到同一提交。
- 使用同一 SQL 文件导入数据。
- 提交问题时附带：报错截图 + 执行命令 + 当前分支和提交号。
