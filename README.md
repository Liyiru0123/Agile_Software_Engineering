# 部署说明

## 1. 拉取代码

```bash
git clone https://github.com/Liyiru0123/Agile_Software_Engineering.git
cd Agile_Software_Engineering
```

## 2. 安装依赖

```bash
composer install
```

## 3. 初始化环境

```bash
copy .env.example .env
php artisan key:generate
```

## 4. 配置数据库

编辑 .env：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning
DB_USERNAME=root
DB_PASSWORD=
```

## 5. 迁移数据库

```bash
php artisan migrate
```
## 导入数据：


```bash
mysql -u root -p english_learning < database/sql/version3.21/generated_article_exercise_dataset.sql
```

## 7. 启动服务

```bash
php artisan config:clear
php artisan serve
```

访问地址：

http://127.0.0.1:8000