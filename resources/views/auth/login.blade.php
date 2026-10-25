
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login RedStock</title>
</head>
<body>

    <h1>RedStock</h1>
    <p>Sistem Informasi Inventaris Barang</p>

    <h2>Login</h2>

    <form action="#" method="POST">
        @csrf

        <div>
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Masukkan username"
                required
            >
        </div>

        <br>

        <div>
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required
            >
        </div>

        <br>

        <button type="submit">Login</button>

    </form>

</body>
</html>
