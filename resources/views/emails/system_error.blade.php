<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="UTF-8">
    <title>Sistem Xətası Bildirişi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        h1 {
            color: #d9534f;
        }

        strong {
            color: #555;
        }

        pre {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #eee;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Sistem Xətası Baş Verdi!</h1>
        <p><strong>Zaman:</strong> {{ $details['time'] }}</p>
        <p><strong>Səviyyə:</strong> {{ $details['level'] }}</p>
        <p><strong>URL:</strong> {{ $details['url'] }}</p>
        <p><strong>İstifadəçi:</strong> {{ $details['user_id'] }}</p>
        <p><strong>Mesaj:</strong></p>
        <p>{{ $details['message'] }}</p>

        @if (!empty($details['file']))
            <p><strong>Fayl:</strong> {{ $details['file'] }}:{{ $details['line'] }}</p>
        @endif

        <h3>Trace:</h3>
        <pre><code>{{ $details['trace'] }}</code></pre>
</body>

</html>
