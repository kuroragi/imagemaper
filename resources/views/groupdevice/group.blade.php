@extends('layouts.main')

@section('container')
    <table class="table table-md mb-3">
        <thead>
            <th>nama</th>
            <th>gambar</th>
            <th>aksi</th>
        </thead>
        <tbody>
            @foreach ($group as $g)
                <tr>
                    <th class="align-content-center">{{ $g->name }}</th>
                    {{-- <th><img src="/img/gdevice/{{ $g->image }}" width="100" alt=""></th> --}}
                    <th><img src="{{ $g->image }}" width="100" alt=""></th>
                    <th class="align-content-center text-center"><a href="/groupdevice/{{ $g->id }}">detail</a></th>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="/groupdevice" method="post" enctype="multipart/form-data">
        @csrf
        <input class="form-control" type="text" name="name">
        <input class="form-control" type="file" name="image">
        <input class="btn btn-primary" type="submit" value="Simpan data">
    </form>
    

@endsection