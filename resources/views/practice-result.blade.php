<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>练习结果</title>
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
        .correct {
            color: green;
            font-weight: bold;
        }
        .wrong {
            color: red;
            font-weight: bold;
        }
        a {
            display: inline-block;
            margin: 15px 10px 0 0;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .gray {
            background: #6b7280;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>答题反馈</h1>

        <p><strong>题目：</strong>{{ $question['title'] }}</p>
        <p><strong>你的答案：</strong>{{ $userAnswer }}</p>
        <p><strong>正确答案：</strong>{{ $question['correct_answer'] }}</p>

        @if ($isCorrect)
            <p class="correct">回答正确</p>
        @else
            <p class="wrong">回答错误</p>
        @endif

        <p><strong>解析：</strong>{{ $question['explanation'] }}</p>
        <p><strong>本次用时：</strong>{{ $duration }} 秒</p>

        <a href="{{ route('practice.show') }}">再做一次</a>
        <a href="{{ route('study.profile') }}" class="gray">查看学习档案</a>
        <a href="{{ url('/') }}" class="gray">返回首页</a>
    </div>
</body>
</html>