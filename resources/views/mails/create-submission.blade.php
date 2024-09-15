<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #4A90E2;
        }

        p {
            line-height: 1.6;
        }

        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 20px;
        }

        .highlight {
            background-color: #eaf4fc;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #d1e3f0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>{{ $messages['title'] }}</h1>

        <p><strong>{{ $messages['regards'] ?? 'Hello Admin,' }}</strong></p>

        <p>{{ $messages['body'] }}</p>

        <p>{{  $messages['body2'] ?? '' }}</p>

        @component('mail::button', ['url' => config('app.url') . $messages['url']])
            Lihat
        @endcomponent

        <p><strong>Salam hormat,</strong></p>
        <p><strong>{{ config('mail.from.name') }}</strong><br>
            {{ config('mail.from.address') }}</p>

        <div class="footer">
            *Catatan: Email ini dikirim secara otomatis. Mohon untuk tidak membalas email ini.*
        </div>
    </div>
</body>

</html>
