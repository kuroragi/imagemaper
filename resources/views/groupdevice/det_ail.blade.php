<!DOCTYPE html>
<html>
<head>
    <title>Image Map</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imagemapster/1.8.0/jquery.imagemapster.min.js"></script>
    <style>
        .area-row {
            margin-bottom: 10px;
        }        
    </style>
</head>
<body>
    <h1>Map Your Image</h1>

    <img id="map-image" src="/img/{{ $odp->gambar }}" usemap="#image-map" alt="Map Image">

    <map name="image-map" id="image-map">
        @foreach($areas as $area)
            <area data-status="{{ $area->status }}" alt="{{ $area->alt }},{{ $area->status }}" title="{{ $area->title }}" href="javascript:void(0);" coords="{{ $area->coords }}" shape="{{ $area->shape }}" 
            {{-- onclick="runFunction('{{ $area->alt }}')" --}}
            >
        @endforeach
    </map>

    <div id="form-container">
        <!-- Initial form with inputs will be appended here -->
    </div>

    <button type="button" id="add-area-btn">Add Area</button>
    <button type="button" onclick="addArea()">Collect Areas</button>
    <button type="button" onclick="saveAreas()">Save Areas</button>

    <h2>Mapped Areas</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Alt</th>
                <th>Title</th>
                <th>Coordinates</th>
                <th>Shape</th>
                <th>Status</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody id="area-table-body">
            @foreach($areas as $area)
                <tr>
                    <td>{{ $area->alt }}</td>
                    <td>{{ $area->title }}</td>
                    <td>{{ $area->coords }}</td>
                    <td>{{ $area->shape }}</td>
                    <td>{{ $area->status }}</td>
                    <td>{{ $area->deskripsi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="/js/script.js"></script>
</body>
</html>
