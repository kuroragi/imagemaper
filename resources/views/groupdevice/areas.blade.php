@extends('layouts.main')

@section('container')

    <h1 class="text-center mb-4">Maping Gambar</h1>

    <div class="mx-2">
        <div id="map-image-container" class=" w-100 text-center">
            <img id="map-image" src="/img/gdevice/{{ $groupdevice->image }}" class="img-fluid rounded shadow" usemap="#areaContainer">
            <map name="areaContainer" id="areaContainer">
                @foreach ($areas as $area)
                    <area data-status="{{ $area->status }}" alt="{{ $area->name }},{{ $area->status }}"
                        title="{{ $area->name }}" href="javascript:void(0);" coords="{{ $area->coordinate }}"
                        shape="{{ $area->shape }}" desc="{{ $area->description }}" id="areabutton{{ $area->id }}">
                @endforeach
            </map>
            <div id="nodeContainer"></div>
        </div>
    </div>


    <div class="row justify-content-center my-3">
        <div id="form-container" class="col-12 mt-3">
            <div class="row border-bottom">
                <div class="col col-1 align-content-center text-bold">
                    Aktif
                </div>
                <div class="col col-2 align-content-center text-bold">
                    Shape
                </div>
                <div class="col col-2 align-content-center text-bold">
                    Status
                </div>
                <div class="col col-3 align-content-center text-bold">
                    Nama Area
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
        {{-- <button type="button" onclick="collectArea()" class="btn btn-primary mb-3">Collect Areas</button> --}}
        <button id="save-area-btn" type="button" onclick="saveAreas('+{{ $groupdevice->id }}+')" class="btn btn-success mb-3">Save
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="area-table-body">
                    @foreach ($areas as $area)
                        <tr id="tr{{ $area->id }}">
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->coordinate }}</td>
                            <td>{{ $area->shape }}</td>
                            <td>{{ $area->status }}</td>
                            <td>{{ $area->description }}</td>
                            <td class="text-danger text-center">
                                <form action="/imagemape/{{ $area->id }}" action="post">
                                    @csrf 
                                    @method('delete')
                                </form>
                                <button type="submit" class="btn btn-danger" kode="{{ $area->id }}" id="deleteareabutton" onclick="return confirm('Yakin Hapus Area {{ $area->name }}?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="infoPanel" class="info-panel">
        <span class="close-btn">&times;</span>
        <div class="p-3 mt-5">
            <h2>Information Panel</h2>
            <table class="table table-borderless" id="infoPanelTable">
                <tbody>
                    <tr>
                        <td>Group Name</td>
                        <td>:</td>
                        <td>{{ $groupdevice->name }}</td>
                    </tr>
                    <tr>
                        <td>Device Name</td>
                        <td>:</td>
                        <td id="area_alt_info"></td>
                    </tr>
                    <tr>
                        <td>Condition</td>
                        <td>:</td>
                        <td id="area_status_info"></td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>:</td>
                        <td id="area_desc_info"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Callout -->
    <div id="callout" class="alert alert-success callout">
        <strong>Berhasil!</strong> Coordinate baru berhasil dimasukan.
    </div>

    <script>
        $('.close-btn').click(function(){
            $('#infoPanel').removeClass('show');
            $('#mainContainer').removeClass('shifted');
            updateArea()
        });
        
        $("#area-table-body").on("click", "#deleteareabutton", function(){
            let kode = $(this).attr('kode');
            $.ajax({
                url: '/imagemap/'+kode,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    // console.log(data);
                    $("#areaContainer #areabutton"+data.id).remove();
                    $("#area-table-body #tr"+data.id).remove();

                    updateArea();
                },
                error: function(e) {
                    console.log(e.responseText);
                }
            });
        });

        $(document).ready(function() {
            
            $('#map-image').on('click', function(e) {

                // Dimensi asli gambar #map-image
                var originalWidth = this.naturalWidth;
                var originalHeight = this.naturalHeight;

                // Dimensi tampilan gambar
                var displayedWidth = $(this).width();
                var displayedHeight = $(this).height();

                // Hitung skala
                scaleX = originalWidth / displayedWidth;
                scaleY = originalHeight / displayedHeight;

                // Koordinat klik dalam dimensi tampilan
                var offset = $(this).offset();
                var clickX = e.pageX - offset.left;
                var clickY = e.pageY - offset.top;

                // Sesuaikan koordinat untuk dimensi asli
                var x = clickX * scaleX;
                var y = clickY * scaleY;

                // Sesuaikan kembali koordinat dengan dimensi gambar
                // var displayX = x / scaleX;
                // var displayY = y / scaleY;

                pointClick++;

                let selectedRadio = getCheckedRadio();
                let activeRow = getActiveRow();

                if (!selectedRadio.length) {
                    pointClick = 0;
                    alert('Please select an area to add coordinates.');
                    return;
                }

                var alt = activeRow.find('[name^="alt"]').val();
                if (alt == null || alt == '') {
                    pointClick = 0;
                    alert('Nama Area Masih Kosong, Mohon Diisi');
                    return;
                }                

                var shape = activeRow.find('select[name^="shape_"]').val();
                // console.log(selectedRadio);

                createNode(clickX, clickY, selectedRadio);

                addPoint(x, y, shape, activeRow);
            });

            $('#map-image').on('mousedown', function() {
                timeoutId = setTimeout(() => {isdblClicked = true;}, 500);
            }).on('mouseup', function() {
                clearTimeout(timeoutId);
            });

            $('#add-area-btn').on('click', function() {
                addAreaRow();
            });

            updateArea();
        });
    </script>


@endsection