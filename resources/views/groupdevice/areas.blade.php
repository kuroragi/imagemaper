@extends('layouts.main')

@section('container')

    <h1 class="text-center mb-4">Map Your Image</h1>

    <div class="mx-2">
        <div id="map-image-container" class=" w-100 text-center">
            <img id="map-image" src="/img/gdevice/{{ $groupdevice->image }}" class="img-fluid rounded shadow" usemap="#image-map">
            <map name="image-map" id="image-map">
                @foreach ($areas as $area)
                    <area data-status="{{ $area->status }}" alt="{{ $area->name }},{{ $area->status }}"
                        title="{{ $area->name }}" href="javascript:void(0);" coords="{{ $area->coordinate }}"
                        shape="{{ $area->shape }}" id="area-button">
                @endforeach
            </map>
        </div>
    </div>

    <div class="row justify-content-center my-3">
        <div id="form-container" class="col-12 mt-3">
            <div class="row border-bottom">
                <div class="col col-1 align-content-center text-bold">
                    Aktif
                </div>
                <div class="col col-3 align-content-center text-bold">
                    Nama Area
                </div>
                <div class="col col-2 align-content-center text-bold">
                    Shape
                </div>
                <div class="col col-2 align-content-center text-bold">
                    Status
                </div>
                <div class="col col-3 align-content-center text-bold">
                    Deskripsi
                </div>
            </div>
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
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->coordinate }}</td>
                            <td>{{ $area->shape }}</td>
                            <td>{{ $area->status }}</td>
                            <td>{{ $area->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="infoPanel" class="info-panel">
        <span class="close-btn">&times;</span>
        <div class="p-3">
            <h4>Information Panel</h4>
            <p>This is the sliding panel content.</p>
            <!-- Panel content can be added here -->
        </div>
    </div>

    <!-- Callout -->
    <div id="callout" class="alert alert-success callout">
        <strong>Berhasil!</strong> Coordinate baru berhasil dimasukan.
    </div>

    <script>
        function saveAreas() {
            if (areasToAdd.length === 0) {
                alert('No areas to save.');
                return;
            }

            // Debugging log to check areasToAdd
            console.log('Areas to add:', areasToAdd);

            $.ajax({
                url: '/imagemap',
                type: 'POST',
                data: {
                    group_id: '{{ $groupdevice->id }}',
                    areas: areasToAdd,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    // console.log(data);

                    alert(data);
                    // data.areas.forEach(function(area) {
                    //     updateTable(area);
                    // });
                    areasToAdd = [];
                },
                error: function(e) {
                    console.log(e.responseText);
                }
            });
        }

        $('#image-map').on('click', '#area-button', function(){
            $('#infoPanel').addClass('show');
            $('#mainContainer').addClass('shifted');
        });

        $('.close-btn').click(function(){
            $('#infoPanel').removeClass('show');
            $('#mainContainer').removeClass('shifted');
            updateArea()
        });

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
                    pointClick = 0;
                    alert('Please select an area to add coordinates.');
                }
                var shape = selectedRadio.find('select[name^="shape_"]').val();
                // console.log(shape);


                selectedRadioCheck(x, y, pointClick, shape);
            });

            $('#map-image').on('mousedown', function() {
                timeoutId = setTimeout(() => {isdblClicked = true;}, 500);
            }).on('mouseup mouseleave', function() {
                clearTimeout(timeoutId);
            });

            $('#add-area-btn').on('click', function() {
                addAreaRow();
            });

            updateArea();
        });
    </script>


@endsection