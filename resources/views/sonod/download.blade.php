<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>সনদ ডাউনলোড</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .download-container {
            text-align: center;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .btn-lg {
            font-size: 1.5rem;
            padding: 15px 30px;
            margin: 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="download-container">
        <h2 class="mb-4">সনদ নম্বরঃ {{ $sonodNo }}</h2>
        <a href="{{ $banglaUrl }}" class="btn btn-primary btn-lg" target="_blank">বাংলা সনদ ডাউনলোড</a>
        <a href="{{ $englishUrl }}" class="btn btn-success btn-lg" target="_blank">English Sonod Download</a>
    </div>
</body>
</html>
