<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Reading - Oral Studio</title>
    
    <!-- 1. 引入 Tailwind CSS (红木风的核心引擎) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. 引入 FontAwesome 图标 (播放、麦克风图标) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- 3. 自定义红木主题配置 (Tailwind 扩展) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mahogany: '#8B4513',      // 红木主色
                        darkWood: '#5D2A18',      // 深色木纹
                        paper: '#FDFBF7',         // 象牙纸张色
                        silkGold: '#EAD8B1',      // 丝绸金
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#F3EFE0]"> <!-- 整个网页背景换成柔和的浅木色 -->

    <!-- 顶栏：沉浸式深木纹导航 -->
    <nav class="bg-darkWood shadow-lg border-b-2 border-mahogany">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a class="text-silkGold font-serif text-2xl font-bold tracking-tighter hover:opacity-80 transition" href="/">
                <i class="fas fa-feather-alt mr-2"></i> English Reading
            </a>
            
            <div class="space-x-4">
                <a class="px-4 py-2 bg-mahogany text-silkGold rounded-lg hover:bg-[#3D1A0D] transition shadow-md font-serif text-sm" href="/articles">
                    <i class="fas fa-book-open mr-1"></i> Library
                </a>
                <button class="text-silkGold opacity-60 hover:opacity-100 transition">
                    <i class="fas fa-user-circle text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- 主内容区：去除 Bootstrap 的默认容器限制，交给 Tailwind 控制 -->
    <div class="mt-0">
        @yield('content')
    </div>

    <!-- 底部：页脚设计 -->
    <footer class="py-12 text-center text-gray-500 text-sm font-serif">
        <div class="h-[1px] w-24 bg-mahogany opacity-20 mx-auto mb-4"></div>
        &copy; 2026 Academic English Oral Training Studio
    </footer>

</body>
</html>