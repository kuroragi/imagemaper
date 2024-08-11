<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table style="border: 1px solid black; margin: 50px 0;">
        <thead>
            <th>nama</th>
            <th>gambar</th>
            <th>aksi</th>
        </thead>
        <tbody>
            @foreach ($odp as $o)
                <th>{{ $o->nama }}</th>
                <th><img src="/img/{{ $o->gambar }}" width="50" alt=""></th>
                <th><a href="/odp/{{ $o->id }}">detail</a></th>
            @endforeach
        </tbody>
    </table>

    <form action="/odp" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" name="nama">
        <input type="file" name="file">
        <input type="submit" value="Simpan data">
    </form>
    
</body>
</html>