
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard kasir | RedStock</title>
</head>
<body>
    <h1>Dashboard kasir</h1>
    <p>Selamat datang di halaman pengelolaan RedStock.</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
