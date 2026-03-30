# 1. 克隆项目
git clone https://github.com/Liyiru0123/Agile_Software_Engineering.git
cd Agile_Software_Engineering

# 2. 安装 PHP 依赖
composer install

# 3. 配置环境变量
copy .env.example .env
php artisan key:generate

# 4. 编辑 .env 文件，配置数据库等参数：（第23-28行）
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning
DB_USERNAME=root
DB_PASSWORD=

# 5. 运行数据库迁移
php artisan migrate

# 6. 运行
php artisan serve