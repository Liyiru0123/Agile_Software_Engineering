<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学习档案</title>
</head>
<body>
    <h1>学习档案</h1>

    <ul>
        <li>当前用户：{{ $currentUser }}</li>
        <li>练习次数：{{ $practiceCount }}</li>
        <li>正确率：{{ $accuracy }}%</li>
        <li>学习时长：{{ $studySeconds }} 秒</li>
    </ul>

    <p>
        <a href="{{ url('/') }}">返回首页</a>
    </p>

    @if (auth()->check())
        <p>当前状态：已登录用户</p>
    @elseif (session()->has('guest'))
        <p>当前状态：游客模式</p>

        <form method="POST" action="{{ route('guest.logout') }}">
            @csrf
            <button type="submit">退出游客模式</button>
        </form>
    @endif
</body>
</html>