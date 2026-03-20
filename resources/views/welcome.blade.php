<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>测试首页</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 30px;
            max-width: 700px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h1 {
            margin-bottom: 20px;
        }
        .btn, button {
            display: inline-block;
            margin: 10px 10px 10px 0;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn.gray, button.gray {
            background: #6b7280;
        }
        .btn.red, button.red {
            background: #dc2626;
        }
        .btn.green, button.green {
            background: #16a34a;
        }
        ul {
            margin-top: 20px;
            line-height: 1.8;
        }
        .status {
            margin: 15px 0;
            padding: 12px 16px;
            background: #f3f4f6;
            border-radius: 6px;
        }
        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>学术英语学习平台 - 功能测试入口</h1>
        <p>当前页面仅用于测试 P0 核心流程，不作为最终 UI。</p>

        {{-- 已登录用户 --}}
        @auth
            <div class="status">
                <strong>当前状态：</strong>已登录　
                <strong>用户：</strong>{{ auth()->user()->name }}
            </div>

            <div class="section-title">功能入口</div>
            <a href="{{ route('practice.show') }}" class="btn green">开始练习</a>
            <a href="{{ route('study.profile') }}" class="btn">学习档案</a>
            <a href="{{ route('identity.choose') }}" class="btn gray">选择身份页面</a>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="red">退出登录</button>
            </form>
        @endauth

        {{-- 未登录状态：再区分普通未登录 / 游客模式 --}}
        @guest
            @if (session()->has('guest'))
                <div class="status">
                    <strong>当前状态：</strong>游客模式
                </div>

                <div class="section-title">功能入口</div>
                <a href="{{ route('practice.show') }}" class="btn green">开始练习</a>
                <a href="{{ route('study.profile') }}" class="btn">学习档案</a>
                <a href="{{ route('identity.choose') }}" class="btn gray">选择身份页面</a>

                <form method="POST" action="{{ route('guest.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="red">退出游客模式</button>
                </form>
            @else
                <div class="status">
                    <strong>当前状态：</strong>未登录
                </div>

                <div class="section-title">功能入口</div>
                <a href="{{ route('register') }}" class="btn">注册</a>
                <a href="{{ route('login') }}" class="btn">登录</a>
                <a href="{{ route('identity.choose') }}" class="btn gray">选择身份 / 游客进入</a>
                <a href="{{ route('practice.show') }}" class="btn green">开始练习</a>
                <a href="{{ route('study.profile') }}" class="btn">学习档案</a>
            @endif
        @endguest

        <ul>
            <li>测试邮箱注册 / 登录</li>
            <li>测试游客模式进入 / 退出</li>
            <li>测试练习页面跳转</li>
            <li>测试答题反馈</li>
            <li>测试学习档案页面显示</li>
        </ul>
    </div>
</body>
</html