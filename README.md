1可以选择性看，别的是必看！！！！然后数据库路由解析也要看！！！！
框架主题是laravel！！！不懂问ai！！！！！

# 1.项目需求
前端基础

登录页和鉴权流程

路由守卫

全局 layout

API 请求封装

环境变量配置

公共 UI 组件基础壳

基础状态管理

后端基础

项目启动骨架

数据库连接

migration

用户认证

JWT/session

权限中间件

统一异常处理

日志

文件上传能力（如果文章封面/资源需要）

# 2.命名统一（一定要统一因为不会有人帮大家合并代码，有问题只能自己改哦）
1. 返回格式统一

例如：

{
  "code": 0,
  "message": "success",
  "data": {}
}
2. 错误码统一

例如：

0：成功

4001：参数错误

4002：未登录

4003：无权限

5001：服务器错误

3. REST 风格统一

例如：

GET /articles

GET /articles/:id

POST /articles

PUT /articles/:id

DELETE /articles/:id

4. 先写接口文档

你可以先把这些业务接口写成文档或 swagger：

用户模块

文章模块

收藏模块

阅读记录模块

练习模块

这样前端同学即使后端没写完，也能先用 mock 数据开发。

# 3.数据库公共主干表设计
建议先定这些：

users

articles

categories

tags

article_tags

reading_history

favorites

如果你们后面有单词本和题目功能，再扩：

vocabulary_notes

questions

question_attempts

这样你搭的是“主干 schema”，别人加功能时在这个基础上扩展。

同时你要规定：

所有表都带 id

都带 created_at, updated_at

是否逻辑删除统一

外键怎么建统一

migration 文件命名统一

# 4.开发规范（这个有人不会吗提交要新建分支提pull request应该没人不会吧）
1. Git 分支规范

建议：

main：稳定版本

dev：开发主分支

feature/xxx：每个人自己的功能分支

例如：

feature/article-detail

feature/vocab-book

feature/practice-page

2. 提交规范

至少统一 commit 风格：

feat: add article api

fix: correct auth middleware

refactor: split article service

3. 合并方式

不要直接往主分支乱推。
统一走 PR / merge request

# 5.环境配置
要有xampp，php，composer，laravel，具体的问ai装好就可以（基本都是上次用过的）
基础版技术栈：

后端框架：Laravel

数据库：MySQL

前端页面：Blade

样式：Bootstrap

前端交互：少量 JavaScript

版本控制：Git + GitHub
# 6.分工细则
你现在这个项目是 **Laravel 单体项目**，所以：
**不需要再单独新建 `frontend/` 和 `backend/` 两个顶层目录。**
都放在同一个 Laravel 项目里了。

## 总架构（李奕儒）

主要改：

```text
routes/web.php
resources/views/layouts/
app/Http/Controllers/ 的整体规划
README.md
database/migrations/ 规范
```

## 前端组

主要改：

```text
resources/views/
resources/css/
resources/js/
```
### 前端负责人 1：用户阅读体验页
主要负责

首页

文章列表页

文章详情页

阅读页排版优化

主要改的部分

resources/views/home.blade.php

resources/views/articles/index.blade.php

resources/views/articles/show.blade.php

resources/views/layouts/app.blade.php

CSS / Bootstrap 样式相关内容

需要完成的内容

首页卡片布局

文章列表展示

筛选/分类展示样式

阅读页字体、间距、按钮、返回等

响应式基础适配

### 前端负责人 2：用户中心与交互页
主要负责

登录/注册页样式

收藏页

阅读记录页

后台文章管理页基础 UI

表单交互

主要改的部分

resources/views/auth/

resources/views/favorites/

resources/views/history/

resources/views/admin/

需要完成的内容

用户中心页面

收藏列表页面

阅读历史列表页面

表单输入体验

页面统一风格

## 后端组

主要改：

```text
app/Http/Controllers/
app/Models/
routes/web.php（按你规定来）
```
### 后端负责人 1：文章模块
主要负责

文章列表

文章详情

分类筛选

搜索（基础版）

主要改的部分

app/Http/Controllers/ArticleController.php

app/Models/Article.php

相关路由

文章查询逻辑

需要完成的内容

获取文章列表

文章详情查询

按分类筛选

按难度筛选

关键字搜索（先简单 like）

### 后端负责人 2：用户与收藏模块
主要负责

登录注册

用户身份

收藏功能

主要改的部分

app/Http/Controllers/Auth/...

app/Http/Controllers/FavoriteController.php

app/Models/User.php

app/Models/Favorite.php

需要完成的内容

用户注册

用户登录

登录状态判断

收藏文章

查看收藏列表

取消收藏

### 后端负责人 3：阅读记录与后台管理模块
主要负责

阅读历史

阅读进度

后台文章增删改

数据管理接口

主要改的部分

app/Http/Controllers/ReadingHistoryController.php

app/Http/Controllers/Admin/ArticleAdminController.php

app/Models/ReadingHistory.php

需要完成的内容

打开文章时记录阅读历史

展示用户历史记录

后台新增文章

后台编辑文章

后台删除文章

## 数据库/数据组

### 数据库负责人 1：Schema & Migration 负责人
主要负责

设计主表关系

写 migration

管理 seed 数据

保证字段统一

跟后端确认模型关系

主要改的部分

database/migrations/

database/seeders/

部分 app/Models/ 关系定义可协助后端完成

需要完成的内容
第一阶段

articles

categories

favorites

reading_histories

users（配合 Laravel 默认用户表）

第二阶段

vocabulary_notes

tags

article_tag

comments（如果要评论）

reading_progress（如果需要更细阅读记录

---

### 数据负责人 2：内容采集与内容结构负责人
主要负责

收集英语阅读文章

清洗文章格式

给文章打标签、分级

组织导入数据库的数据格式

主要改的部分

database/seeders/

数据导入脚本

文档目录下的数据说明文件

如果你们有后台上传，也可协助定义上传格式

需要完成的内容

收集文章文本

定义 level（Easy / Intermediate / Advanced）

定义 category（Science / Life / Culture...）

统一 author/source 格式

准备初始文章样本 20 篇