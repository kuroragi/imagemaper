<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Image Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.0/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imagemapster/1.8.0/jquery.imagemapster.min.js"></script>
    <style>
        .area-row {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Map Your Image</h1>

        <div class="mx-2">
            <div id="map-image-container" class="justify-content-center">
                <img id="map-image" src="/img/{{ $odp->gambar }}" class="img-fluid rounded shadow" usemap="#image-map">
                <map name="image-map" id="image-map">
                    @foreach ($areas as $area)
                        <area data-status="{{ $area->status }}" alt="{{ $area->alt }},{{ $area->status }}"
                            title="{{ $area->title }}" href="javascript:void(0);" coords="{{ $area->coords }}"
                            shape="{{ $area->shape }}" {{-- onclick="runFunction('{{ $area->alt }}')" --}}>
                    @endforeach
                </map>
            </div>
        </div>
        <div class="row justify-content-center">
            <div id="form-container" class="col-12 col-md-10 col-lg-8 my-3">
                <!-- Initial form with inputs will be appended here -->
            </div>
        </div>

        <div class="my-4">
            <button id="add-area-btn" type="button" class="btn btn-primary mb-3">Add Area</button>
            <button type="button" onclick="addArea()" class="btn btn-primary mb-3">Collect Areas</button>
            <button id="save-area-btn" type="button" onclick="saveAreas()" class="btn btn-success mb-3">Save
                Areas</button>
        </div>

        <div class="mt-4">
            <h2>Areas List</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
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
                        @foreach ($areas as $area)
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
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/js/script.js"></script>

        <script>
            function saveAreas() {
                if (areasToAdd.length === 0) {
                    alert('No areas to save.');
                    return;
                }

                // Debugging log to check areasToAdd
                console.log('Areas to add:', areasToAdd);

                $.ajax({
                    url: '{{ route('save.areas') }}',
                    type: 'POST',
                    data: {
                        odp_id: 1,
                        areas: areasToAdd,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        console.log(data);

                        alert(data);
                        data.areas.forEach(function(area) {
                            updateTable(area);
                        });
                        areasToAdd = [];
                    },
                    error: function(e) {
                        console.log(e.responseText);
                    }
                });
            }

            $(document).ready(function() {
                $('#map-image').on('click', function(e) {
                    var offset = $(this).offset();
                    var x = e.pageX - offset.left;
                    var y = e.pageY - offset.top;
                    pointClick++;
                    // if (isFirstClick) {
                    //     // Simpan koordinat klik pertama
                    //     firstX = x;
                    //     firstY = y;

                    //     // Buat elemen baru yang dapat di-drag dan resize
                    //     $newArea = $('<div></div>')
                    //         .css({
                    //             position: 'absolute',
                    //             left: `${firstX}px`,
                    //             top: `${firstY}px`,
                    //             width: '10px',
                    //             height: '10px',
                    //             backgroundColor: 'rgba(128, 128, 128, 0.75)',
                    //             border: '1px solid #000'
                    //         })
                    //         .appendTo('#map-image-container') // Sesuaikan dengan container gambar Anda
                    //         .draggable({
                    //             containment: '#map-image-container',
                    //             stop: function(event, ui) {
                    //                 // Update koordinat ketika elemen dipindahkan
                    //                 updateCoords(ui.helper);
                    //             }
                    //         })
                    //         .resizable({
                    //             containment: '#map-image-container',
                    //             stop: function(event, ui) {
                    //                 // Update koordinat ketika elemen diubah ukurannya
                    //                 updateCoords(ui.helper);
                    //             }
                    //         });

                    //     isFirstClick = false;
                    // } else {
                    //     // Klik kedua: hitung ukuran berdasarkan klik kedua dan tambahkan area
                    //     const width = Math.abs(x - firstX);
                    //     const height = Math.abs(y - firstY);

                    //     $newArea.css({
                    //         width: `${width}px`,
                    //         height: `${height}px`
                    //     });

                    //     // Update koordinat input
                    //     updateCoords($newArea);

                    //     // Reset status klik
                    //     isFirstClick = true;
                    // }
                    var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
                    if (!selectedRadio.length) {
                        alert('Please select an area to add coordinates.');
                    }
                    var shape = selectedRadio.find('select[name^="shape_"]').val();
                    // console.log(shape);


                    selectedRadioCheck(x, y, pointClick, shape);
                });

                $('#add-area-btn').on('click', function() {
                    addAreaRow();
                });

                updateArea();
            });
        </script>
</body>

</html>
