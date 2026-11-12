<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
</head>
<body>
    <h1>Berhasil Login sebagai Mahasiswa!</h1>
    <p>Halo, {{ Auth::user()->name ?? 'Mahasiswa' }}</p>
</body>
</html>