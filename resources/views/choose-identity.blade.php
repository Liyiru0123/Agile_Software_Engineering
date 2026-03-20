<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>选择进入方式</title>
</head>
<body>
    <h1>请选择进入方式</h1>

    <p>你可以登录账号、注册新账号，或者先以游客身份体验。</p>

    <p>
        <a href="{{ route('login') }}">去登录</a>
    </p>

    <p>
        <a href="{{ route('register') }}">去注册</a>
    </p>

    <form method="POST" action="{{ route('guest.start') }}">
        @csrf
        <button type="submit">游客进入</button>
    </form>

    <p>
        <a href="{{ url('/') }}">返回首页</a>
    </p>
</body>
</html>