<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>练习页面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }
        .box {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h1 {
            margin-bottom: 20px;
        }
        .option {
            margin: 12px 0;
        }
        button, a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .back {
            background: #6b7280;
            margin-left: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>练习页面</h1>

        <p><strong>{{ $question['title'] }}</strong></p>

        <form method="POST" action="{{ route('practice.submit') }}">
            @csrf

            @foreach ($question['options'] as $key => $value)
                <div class="option">
                    <label>
                        <input type="radio" name="answer" value="{{ $key }}">
                        {{ $key }}. {{ $value }}
                    </label>
                </div>
            @endforeach

            @error('answer')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">提交答案</button>
            <a href="{{ url('/') }}" class="back">返回首页</a>
        </form>
    </div>
</body>
</html>