<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Country Info System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .row { display: flex; gap: 24px; align-items: flex-start; }
        .card { border: 1px solid #ddd; padding: 12px; border-radius: 8px; }
        a { color: #0b5cff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        input, select { padding: 8px; }
    </style>
</head>
<body>
    <h2><a href="{{ route('countries.index') }}">Country Info System</a></h2>
    @yield('content')
</body>
</html>
