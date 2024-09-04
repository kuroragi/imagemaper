<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Image Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.0/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.0/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imagemapster/1.8.0/jquery.imagemapster.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.js"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script type="text/javascript">
        let assetData = @json($asset ?? []);
    </script> -->
    <!-- <script src="/js/imagemaper.js" defer></script> -->

    <style>
        .area-row {
            margin-bottom: 10px;
        }
        #map-image-container{
            position: relative;
            display: inline-block;
        }
        .callout {
            display: none; /* Initially hidden */
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            width: 300px;
        }
        .info-panel {
            position: fixed;
            top: 0;
            right: -500px;
            width: 500px;
            height: 100%;
            background-color: #f8f9fa;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
            transition: right 0.5s ease-in-out;
            z-index: 1040;
        }
        .info-panel.show {
            right: 0;
        }
        #main-container {
            transition: margin-right 0.5s ease-in-out;
        }
        #main-container.shifted {
            margin-right: 500px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
        }
        .node {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            /* pointer-events: none; */
            z-index: 1000;
        }
    </style>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</head>

<body>
    <div id="main-container" class="container my-5">
        
        <h1 class="text-center mb-4">Maping Gambar</h1>

        <div class="mx-2">
            <div id="map-image-container" class="text-center" style="width: 110%;">
                <img id="map-image" src="{{ $groupdevice->image }}" class="img-fluid rounded shadow" usemap="#areaContainer">
                <map name="areaContainer" id="areaContainer">
                    @foreach ($areas as $area)
                        <area data-status="{{ $area->status }}" alt="{{ $area->name }},{{ $area->status }}"
                            title="{{ $area->name }}" href="javascript:void(0);" coords="{{ $area->coordinate }}"
                            shape="{{ $area->shape }}" desc="{{ $area->description }}" id_group="{{ $area->id_asset_group }}" id="savedArea_{{ $area->id }}" kode="{{ $area->id }}">
                    @endforeach
                </map>
                <div id="nodeContainer">
                    @foreach ($nodes as $node)
                    {{-- @dd($node['x']) --}}
                        <div id="{{ $node['nodeName'] }}" nodeIndex="{{ $node['nodeIndex'] }}" x="{{ $node['x'] }}"  y="{{ $node['y'] }}" class="node ui-draggable ui-draggable-handle d-none" style="top:{{ $node['y'] - 5 }}px; left:{{ $node['x'] - 5 }}px;"></div>
                    @endforeach
                </div>
            </div>
        </div>


        <div class="row justify-content-center my-3">
            <div id="form-container" class="col-12 mt-3">
                <div class="row border-bottom pb-2">
                    <div class="col col-1 align-content-center text-bold">
                        Aktif
                    </div>
                    <div class="col col-1 align-content-center text-bold">
                        Shape
                    </div>
                    <div class="col col-2 align-content-center text-bold">
                        Status
                    </div>
                    <div class="col col-2 align-content-center text-bold">
                        Nama Area
                    </div>
                    <div class="col col-2 align-content-center text-bold">
                        Asset
                    </div>
                    <div class="col col-4 align-content-center text-bold">
                        Deskripsi
                    </div>
                </div>
                <!-- Initial form with inputs will be appended here -->
            </div>
        </div>

        <div class="newNodeArea"></div>

        <div class="my-4">
            <button id="add-area-btn" type="button" class="btn btn-primary mb-3">Add Area</button>
            {{-- <button type="button" onclick="collectArea()" class="btn btn-primary mb-3">Collect Areas</button> --}}
            <button id="save-area-btn" type="button" onclick="saveAreasApi('{{ $groupdevice->id }}')" class="btn btn-success mb-3">Save
                Areas</button>
        </div>

        <div id="savedFormContainer">
            @foreach ($areas as $area)
                <div class="area-row row" id="area-row-{{ $area->id }}">
                    <input type="hidden" id="area_id" value="{{ $area->id }}">
                    <input type="hidden" name="id_asset_group_{{ $area->id }}" id="id_asset_group_{{ $area->id }}" value="{{ $area->id_asset_group }}">
        
                    <div class="col col-1 align-content-center text-center">
                        <input type="radio" id="savedRadio_{{ $area->id }}" name="selected_area" value="{{ $area->id }}">
                    </div>
        
                    <input type="text" class="form-control" name="coords_{{ $area->id }}" id="coords_{{ $area->id }}" placeholder="Coordinates" value="{{ $area->coordinate }}" readonly>
        
                    <div class="col col-1 align-content-center text-center" required>
                        <select class="form-control" name="shape_{{ $area->id }}" id="shape_{{ $area->id }}">
                            <option @if ($area->shape == 'rect') selected @endif  value="rect">Rectangle</option>
                            <option  @if ($area->status == 'circle') selected @endif value="circle">Circle</option>
                            <option  @if ($area->status == 'poly') selected @endif value="poly">Polygon</option>
                        </select>
                    </div>
        
                    <div class="col col-2 align-content-center text-center">
                        <select class="form-control" name="status_{{ $area->id }}" id="status_{{ $area->id }}" required>
                            <option @if ($area->status == 'kosong') selected @endif value="kosong">Kosong</option>
                            <option @if ($area->status == 'baik') selected @endif  value="baik">Baik</option>
                            <option @if ($area->status == 'rusak') selected @endif  value="rusak">Rusak</option>
                        </select>
                    </div>
        
                    <div class="col col-2 align-content-center text-center">
                        <input type="text" class="form-control" name="alt_{{ $area->id }}" id="alt_{{ $area->id }}" placeholder="Name Area" value="{{ $area->name }}" required>
                    </div>
        
                    <div class="col col-2 align-content-center text-center">
                        <select class="form-control" name="asset_{{ $area->id }}" id="asset_{{ $area->id }}">
                            <option value="">Tidak Ada Asset</option>
                            {{-- ${assetOption} --}}
                        </select>
                    </div>
        
                    <div class="col col-3 align-content-center text-center">
                        <textarea class="form-control" name="description_{{ $area->id }}" id="description_{{ $area->id }}" placeholder="Description" required>{{ $area->description }}</textarea>
                    </div>
        
                    <div class="col col-1 align-content-center text-center" id="buttonArea_{{ $area->id }}">
                        <div id="deleteArea">
                            <button class="btn btn-danger" id="removeArea" areaID="{{ $area->id }}"><i class="fa fa-trash"></i></button>
                        </div>
                        <div id="updateArea" class="d-none">
                            <button class="btn btn-primary" id="saveArea" areaID="{{ $area->id }}"><i class="fa fa-floppy-disk"></i></button>
                            <button class="btn btn-danger" id="cancelSaveArea" areaID="{{ $area->id }}"><i class="fa fa-xmark-circle"></i></button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-none" id="savedDataOrigin">
            @foreach ($areas as $area)
                <div id="origindata_{{ $area->id }}" areaID="{{ $area->id }}" id_asset_group="{{ $area->id_asset_group }}" coordinate="{{ $area->coordinate }}" shape="{{ $area->shape }}" status="{{ $area->status }}" alt="{{ $area->name }}" id_asset="{{ $area->id_asset }}" description="{{ $area->description }}" meta="{{ $area->meta }}" device_type="{{ $area->device_type }}"></div>
            @endforeach
        </div>

        {{-- <div class="mt-4">
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
                                <td class="text-wrap" style="width: 30%;">{{ $area->description }}</td>
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
        </div> --}}

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
            <div class="p-3 mt-3">
                <button class="btn btn-warning w-100" code="0" id="editAreaButton"><i class="fa fa-edit"></i></button>
            </div>
        </div>

        <!-- Callout -->
        <div id="callout" class="alert alert-success callout">
            <strong>Berhasil!</strong> Coordinate baru berhasil dimasukan.
        </div>
    </div>

<script>
    

    $(document).ready(function() {
        
        $('#map-image').on('click', function(e) {

            let imageScale = getImageScale();

            // Koordinat klik dalam dimensi tampilan
            var offset = $(this).offset();
            var clickX = e.pageX - offset.left;
            var clickY = e.pageY - offset.top;

            // Sesuaikan koordinat untuk dimensi asli
            var x = clickX * imageScale.scaleX;
            var y = clickY * imageScale.scaleY;

            // Sesuaikan kembali koordinat dengan dimensi gambar
            // var displayX = x / scaleX;
            // var displayY = y / scaleY;

            pointClick++;

            if (!getCheckedRadio().length) {
                resetPointClick();
                alert('Please select an area to add coordinates.');
                return;
            }

            var alt = getAltActivedRow();
            if (alt == null || alt == '') {
                resetPointClick();
                alert('Nama Area Masih Kosong, Mohon Diisi');
                return;
            }

            createNode(clickX, clickY);

            addPoint(x, y);
        });

        $('#map-image').on('mousedown', function() {
            timeoutId = setTimeout(() => {isdblClicked = true;}, 500);
        }).on('mouseup', function() {
            clearTimeout(timeoutId);
        });

        $('#add-area-btn').on('click', function() {
            addAreaRow();
        });

        renderArea();

        $("#savedFormContainer").on("click", "#savedRadio", function (e) {
            let id = $(this).val();
            $("#nodeContainer [id*='savedNode_").addClass("d-none");
            $("#nodeContainer [id*='savedNode_"+id+"_']").removeClass("d-none");
        });
    });
</script>

<script>
    $('.close-btn').click(function(){
        $('#infoPanel').removeClass('show');
        $('#mainContainer').removeClass('shifted');
        renderArea()
    });

    $("#infoPanel").on("click", "#editAreaButton", function(e){
        let id = $(this).attr("code");
        $("#nodeContainer [id*='savedNode_").addClass("d-none");
        $("#nodeContainer [id*='savedNode_"+id+"_']").removeClass("d-none");
        $('#infoPanel').removeClass('show');
        $('#mainContainer').removeClass('shifted');

        $("#savedFormContainer [id*='savedRadio_']").prop("checked", false);
        $("#savedFormContainer #savedRadio_"+id).prop("checked", true);
        $("#savedFormContainer #savedRadio_"+id).closest().css("backgroundColor", "ffff00");
    })
    
    $("#area-table-body").on("click", "#deleteareabutton", function(){
        let kode = $(this).attr('kode');
        $.ajax({
            url: '/deleteMap/'+kode,
            type: 'DELETE',
            // data: {
            //     _token: '{{ csrf_token() }}'
            // },
            success: function(data) {
                // console.log(data);
                $("#areaContainer #savedArea_"+data.id).remove();
                $("#area-table-body #tr"+data.id).remove();

                renderArea();
            },
            error: function(e) {
                console.log(e.responseText);
            }
        });
    });

    $("#form-container").on("click", "#removeArea", function(){
        let areaID = $(this).attr("areaID");
        removeArea(areaID);
    })
</script>


<script>
const image = $('#map-image');
const areaContainer = $('#areaContainer')
const nodeContainer = $('#nodeContainer');
const selectedAreas = new Set();
const newNodeName = 'newNode_';
const newAreaName = 'newArea_';
const savedAreaName = 'savedArea_';
const savedNodeName = 'savedNode_';
const rectNode = 2,
    circleNode = 2;

let scaleX;
let scaleY;
let selectedCoords = [];
let newAreasToAdd = [];
let areasToAdd = [];
let areaCount = 0;
let pointClick = 0;
let timeoutId = 0;
let isFirstClick = true;
let isFirstClickCircle = true;
let isFirstAreaAdd = true;
let isdblClicked = false;
let firstX, firstY, $newArea, xOne, yOne;
// let selectedRadio;
// let activeRow;

// function selectedRadioCheck(x, y, shape, selectedRadio) {
//     if (shape === 'rect' || shape === 'circle') {
//         let radius = 2;
//         if (pointClick <= radius) {
//             addPoint(x, y, shape, selectedRadio);
//         } else {
//             resetPointClick();
//         }
//     }else{
//         addPoint(x, y, shape, selectedRadio);
//     }
// }

function addPoint(x, y) {
    console.log(`X: ${x}, Y: ${y}`);
    
    let alt = getAltActivedRow();
    let status = getStatusActivedRow();
    let description = getDescriptionActivedRow();
    let shape = getShapeActivedRow();
    if (shape === 'rect') {
        addSelectedCoords(x, y);

        if (selectedCoords.length === rectNode) {
            let coords = selectedCoords.map(function(pt) {
                return pt.x + ',' + pt.y;
            }).join(',');

            
            updateCheckedRadioCoord(coords);

            let areaId = getCheckedRadioValue();

            let newArea = {
                areaId: areaId,
                alt: alt,
                coords: coords,
                shape: shape,
                status: status,
                description: description,
            };

            resetPointClick();
            resetSelectedCoords();
            updateAreaOnMap(newAreaName, newArea);
        }
    } else if (shape === 'circle') {
        if (isFirstClickCircle) {
            xOne = x;
            yOne = y;

            isFirstClickCircle = false;
        } else if (pointClick === circleNode && isFirstClickCircle === false) {
            let coords = xOne + ',' + yOne + ',' + radiusCalc(x, y);

            updateCheckedRadioCoord(coords);

            let areaId = getCheckedRadioValue();

            let newArea = {
                areaId: areaId,
                alt: alt,
                coords: coords,
                shape: shape,
                status: status,
                description: description,
            };

            resetPointClick();
            isFirstClickCircle = true;
            resetSelectedCoords();
            updateAreaOnMap(newAreaName, newArea);
        }
    } else if (shape === 'poly') {
        addSelectedCoords(x, y);

        if (selectedCoords.length >= 3 && isdblClicked === true) {
            
            let coords = selectedCoords.map(function(pt) {
                return pt.x + ',' + pt.y;
            }).join(',');

            updateCheckedRadioCoord(coords);

            let areaId = getCheckedRadioValue();

            let newArea = {
                areaId: areaId,
                alt: alt,
                coords: coords,
                shape: shape,
                status: status,
                description: description,
            };

            isdblClicked = false;
            resetPointClick();
            resetSelectedCoords();
            updateAreaOnMap(newAreaName, newArea);
        }
    }
}

function addAreaRow() {
    areaCount++;
    // let assetOption = assetData.map(asset => `<option value="${asset}">${asset.name}</option>`).join('');
    let assetOption = [];
    let newRow = `
        <div class="area-row row" id="area-row-${areaCount}">
            <input type="hidden" id="area_id" value="${areaCount}">

            <div class="col col-1 align-content-center text-center">
                <input type="radio" name="selected_area" value="${areaCount}" checked>
            </div>

            <input type="text" class="form-control" name="coords_${areaCount}" placeholder="Coordinates" readonly>

            <div class="col col-1 align-content-center text-center" required>
                <select class="form-control" name="shape_${areaCount}">
                    <option value="rect">Rectangle</option>
                    <option value="circle">Circle</option>
                    <option value="poly">Polygon</option>
                </select>
            </div>

            <div class="col col-2 align-content-center text-center">
                <select class="form-control" name="status_${areaCount}" id="status" required>
                    <option value="kosong">Kosong</option>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>

            <div class="col col-2 align-content-center text-center">
                <input type="text" class="form-control" name="alt_${areaCount}" id="alt" placeholder="Name Area" required>
            </div>

            <div class="col col-2 align-content-center text-center">
                <select class="form-control" name="asset_${areaCount}" id="asset">
                    <option value="">Tidak Ada Asset</option>
                    ${assetOption}
                </select>
            </div>

            <div class="col col-3 align-content-center text-center">
                <textarea class="form-control" name="description_${areaCount}" placeholder="Description" required></textarea>
            </div>

            <div class="col col-1 align-content-center text-center">
                <button class="btn btn-danger" id="removeArea" areaID="${areaCount}"><i class="fa fa-trash"></i></button>
            </div>
        </div>
    `;
    $('#form-container').append(newRow);
}

function updateAreaOnMap(areaName ,area) {
    
    $("#areaContainer #"+areaName+area.areaId) ? $("#areaContainer #"+areaName+area.areaId).remove() : '';

    $("#nodeContainer #new-node-"+area.areaId) ? $("#nodeContainer #new-node-"+area.areaId+"-"+pointClick).remove() : '';

    addAreaOnMap(areaName, area);
    runCallout();
    renderArea();
}

function collectArea() {

    areasToAdd = [];
    $('.area-row').each(function() {
        // let formData = $(this).find('input, select, textarea').serializeArray();
        let alt = $(this).find('[name^="alt"]').val();
        let coords = $(this).find('[name^="coords"]').val();
        let id_asset = $(this).find('[name^="asset"]').val();
        let shape = $(this).find('[name^="shape"]').val();
        let status = $(this).find('[name^="status"]').val();
        let description = $(this).find('[name^="description"]').val();

        let areaData = {
            alt: alt,
            coords: coords,
            id_asset: id_asset,
            shape: shape,
            status: status,
            description: description
        };


        areasToAdd.push(areaData);
    });

    if (areasToAdd.length === 0) {
        alert('No areas to add.');
        return;
    }

    // Tambahkan area ke peta dengan warna abu-abu
    areasToAdd.forEach(area => {
        addAreaOnMap(newAreaName, area);

        let newRow = `
            <tr>
                <td>${area.alt}</td>
                <td>${area.alt}</td>
                <td>${area.coords}</td>
                <td>${area.shape}</td>
                <td>${area.status}</td>
                <td>${area.description}</td>
            </tr>`;

        $('#area-table-body').append(newRow);
    });

    // renderArea();
}

function saveAreas(groupId) {
    collectArea();

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
            id_group: groupId,
            areas: areasToAdd,
            // _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            // console.log(data);

            // alert(data);
            // data.areas.forEach(function(area) {
            //     addAreaRowTable(area);
            // });
            // areasToAdd = [];
            location.reload();
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}

function saveAreasApi(groupId) {
    collectArea();

    if (areasToAdd.length === 0) {
        alert('No areas to save.');
        return;
    }


    // Debugging log to check areasToAdd
    console.log('Areas to add:', areasToAdd);


    $.ajax({
        url: '/imageMapApi',
        type: 'POST',
        data: {
            id_group: groupId,
            areas: areasToAdd,
            // _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            console.log(data);

            // alert(data);
            // data.areas.forEach(function(area) {
            //     addAreaRowTable(area);
            // });
            // areasToAdd = [];
            location.reload();
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}

function updateAreaApi(areaID, areaData) {
    console.log(areaData);
    
    

    $.ajax({
        url: '/updateAreaSavedApi',
        type: 'POST',
        data: {
            id: areaID,
            areas: areaData,
            // _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            console.log(data);

            updateSavedCoordsArea(areaData.coords, areaID);
            RowCoordUpdatedUpdated(areaID);

            // alert(data);
            // data.areas.forEach(function(area) {
            //     addAreaRowTable(area);
            // });
            // areasToAdd = [];
            // location.reload();
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}

$('.close-btn').click(function(){
    $('#infoPanel').removeClass('show');
    $('#mainContainer').removeClass('shifted');
    // renderArea()
});

function renderArea() {
    $('#map-image').mapster({
        fillColor: 'ffff00',
        singleSelect: true,
        mapKey: 'alt',
        listKey: 'alt',
        areas: [{
                key: 'kosong',
                fillColor: '808080',
                selected: true,
            },
            {
                key: 'baik',
                fillColor: '00ff00',
                selected: true,
            },
            {
                key: 'rusak',
                fillColor: 'ff0000',
                selected: true,
            }
        ],
        onClick: function(e) {
            const key = e.key; // Ambil key area yang dipilih
            const areaId = $(this).attr('id'); // Dapatkan ID area yang diklik
            const areaCode = $(this).attr('kode'); // Dapatkan ID area yang diklik
            
            
            if (areaId.startsWith('savedArea_')) {
                $('#infoPanel').addClass('show');
                $('#infoPanel #editAreaButton').attr('code', areaCode);
                $('#mainContainer').addClass('shifted');
                updateInfoPanel($(this));
            } else if (areaId.startsWith(_newArea)) {
                // Area dengan ID '${_newArea}' tidak bisa dipilih
                return false; // Mencegah area untuk dipilih
            }

            return true; // Biarkan Mapster melakukan default action-nya
        },
        onConfigured: function() {
            $('#map-image').mapster('set', ['kosong,baik,rusak']);
        },
    });

    // $("#map-image").mapster('rebind', true);
}

function addAreaRowTable(area) {
    $('#area-table-body').append(
        '<tr>' +
        '<td>' + area.alt + '</td>' +
        '<td>' + area.title + '</td>' +
        '<td>' + area.coords + '</td>' +
        '<td>' + area.shape + '</td>' +
        '<td>' + area.status + '</td>' +
        '<td>' + area.description + '</td>' +
        '</tr>'
    );
}

function updateCoords($element) {
    const left = parseInt($element.css('left'), 10);
    const top = parseInt($element.css('top'), 10);
    const width = $element.width();
    const height = $element.height();

    const coords = `${left},${top},${left + width},${top + height}`;
    // $('input[name="coords_' + $('input[name="selected_area"]:checked').val() + '"]').val(coords);
}

function addAreaOnMap(areaName, area) {
    let areaRow = `
    <area data-status="${area.status}" alt="${area.alt},${area.status}"
                title="${area.alt}" href="javascript:void(0);" coords="${area.coords}"
                shape="${area.shape}" id="${areaName}${area.areaId}" kode="${area.areaId}">
    `;
    $('#areaContainer').append(areaRow);
}

function addSavedMap(area) {
    console.log('updateSavedMap()');
    
    let areaRow = `
    <area data-status="${area.status}" alt="${area.alt},${area.status}"
                title="${area.alt}" href="javascript:void(0);" coords="${area.coords}"
                shape="${area.shape}" id="savedArea_${area.areaId}" kode="${area.areaId}">
    `;
    $('#areaContainer').append(areaRow);
}

function runCallout() {
    // Simulate a function running
    setTimeout(function() {
        // Show the callout once the function is done
        $('#callout').fadeIn(100);

        // Hide the callout after 3 seconds
        setTimeout(function() {
            $('#callout').fadeOut(500);
        }, 2000);
    }, 500); // Simulate 1 second delay
}

function updateInfoPanel(area) {
    let p = area;
    console.log(p.attr('desc'));
    
    let alt = p.attr('alt').split(",")[0];
    let status = p.attr('data-status');
    let desc = p.attr('desc');

    

    $("#infoPanelTable #area_alt_info").html(alt);
    $("#infoPanelTable #area_status_info").html(status);
    $("#infoPanelTable #area_desc_info").html(desc);
    
}

function createNode(x, y){


    let areaId = getCheckedRadioValue();

    const ID = newNodeName+areaId+'_'+pointClick;

    let nodeIndex = pointClick - 1;

    // buat elemen node
    let node = $("<div id='"+ID+"' nodeIndex='"+nodeIndex+"' x='"+x+"' y='"+y+"' class='node'></div>");

    // console.log(x-5+', '+y-5);
    let _newnode1 = $(`<div id="nna${ID}"><span>{"new Node" => ${ID}, "X" => ${x}, "Y" => ${y}}</span></div>`);
    

    // Atur posisi node berdasarkan klik
    node.css({
        'top': (y - 5) + 'px',
        'left': (x - 5) + 'px',
    });

    $("#nodeContainer").append(node);
    $("#newNodeArea").append(_newnode1);

    createDraggableNode(ID, areaId, nodeIndex);

}

$("#nodeContainer [id*='savedNode_']").draggable({
    containment: "#map-image-container",
    stop: function(event, ui){
        
        let areaID = getSavedCheckedRadioValue();
        let nodeIndex = $(this).attr('nodeIndex');
        let nodeID = savedNodeName+areaID+"_"+(parseInt(nodeIndex) + 1);
        
        updateSavedCoordsFromNode(nodeID, areaID, nodeIndex);
        savedRowCoordUpdated(areaID);
    }
})

function createDraggableNode(nodeID, areaID, nodeIndex){
    $("#"+nodeID).draggable({
        containment: "#map-image-container", // Pastikan node tetap berada dalam batas gambar
        stop: function(event, ui){
            updateCoordsFromNode(nodeID, areaID, nodeIndex)
        }
    })
    // .resizable({
    //     aspecRatio: true, // Menjaga proporsi saat di-resize
    //     handles: "all",
    //     resize: function(event, ui){
    //         updateCoordsFromNode(nodeID, ui.size, ui.position);
    //     }

    // })
}

function updateCoordsFromNode(nodeID, areaID, nodeIndex){
    let areaShape = $("#areaContainer").find("[id*='newArea_"+areaID+"']").attr('shape');
    let nodes = $("#nodeContainer").find("[id*='newNode_"+areaID+"_']");

    let newNodeArea = $("#newNodeArea #"+areaID);

    let newCoords;
    if(areaShape === 'circle'){
        nodes.map(function (index, element) {
            if(index === 0){
                xOne = $(element).css('left').replace('px', '') * scaleX + 5;
                yOne = $(element).css('top').replace('px', '') * scaleY + 5;
            }else{
                let x = $(element).css('left').replace('px', '') * scaleX + 5;
                let y = $(element).css('top').replace('px', '') * scaleY + 5;
                
                newCoords = xOne+","+yOne+","+radiusCalc(x, y);
            }
            
        });
    }else{
        nodes.map(function (index, element) {
            let x, y;
            if(index === nodeIndex){
                x = $(element).css('left').replace('px', '') * scaleX + 5;
                y = $(element).css('top').replace('px', '') * scaleY + 5;
                let _newnode1 = '{"new Node" => '+areaID+', "X" => '+x+', "Y" => '+y+'},';
                console.log(`xAwal: ${$(element).css('left').replace('px', '')}, yAwal: ${$(element).css('top').replace('px', '')},\n xScaled: ${x}, yScaled: ${y}`);
                
                
                $("#newNodeArea #nna"+nodeID+" span").text(_newnode1);
            }else{
                x = $(element).css('left').replace('px', '') * scaleX + 5;
                y = $(element).css('top').replace('px', '') * scaleY + 5;
                
            }
            addSelectedCoords(x, y);
        });
        
        newCoords = selectedCoords.map(function(pt) {
            return pt.x + ',' + pt.y;
        }).join(',');
    }


    updateCheckedRadioCoord(newCoords);

    resetSelectedCoords();
    updateCoordsArea(newCoords, areaID);
    
    // let newCoords = nodes.map(function(pt) {
    //     console.log(pt.attr('x'));
        
    // });
    
    
    // Memperbarui koordinat berdasarkan posisi node yang di-drag
    // const x = position.left;
    // const y = position.top;

    // let _coords = getActiveRow().find('[name^="coords"]').val()
    // $("input[name='coords_"+nodeID+"']").val(x+","+y);
}

function updateSavedCoordsFromNode(nodeID, areaID, nodeIndex){
    
    let areaShape = $("#areaContainer").find("[id*='savedArea_"+areaID+"']").attr('shape');
    let nodes = $("#nodeContainer").find("[id*='savedNode_"+areaID+"_']");

    // let newNodeArea = $("#newNodeArea #"+areaID);

    // console.log(getImageOriginalSize());
    // console.log(getImageDisplayedSize());

    let imageScale = getImageScale();
    
    

    let newCoords;
    if(areaShape === 'circle'){
        nodes.map(function (index, element) {
            if(index === 0){
                xOne = $(element).css('left').replace('px', '') * imageScale.scaleX + 5;
                yOne = $(element).css('top').replace('px', '') * imageScale.scaleY + 5;
            }else{
                let x = $(element).css('left').replace('px', '') * imageScale.scaleX + 5;
                let y = $(element).css('top').replace('px', '') * imageScale.scaleY + 5;
                
                newCoords = xOne+","+yOne+","+radiusCalc(x, y);
            }
            
        });
    }else{
        nodes.map(function (index, element) {
            let x, y;
            if(index === nodeIndex){
                
                console.log($(element).css('left').replace('px', ''));
                x = ($(element).css('left').replace('px', '') * imageScale.scaleX);
                y = ($(element).css('top').replace('px', '') * imageScale.scaleY);
                let _newnode1 = '{"new Node" => '+areaID+', "X" => '+x+', "Y" => '+y+'},';
                console.log(areaID);
                console.log(x+", "+y);
                
                
                // $("#newNodeArea #nna"+nodeID+" span").text(_newnode1);
            }else{
                
                x = ($(element).css('left').replace('px', '') * imageScale.scaleX);
                y = ($(element).css('top').replace('px', '') * imageScale.scaleY);
                console.log(x+", "+y);
                
            }
            
            addSelectedCoords(x, y);
        });
        
        newCoords = selectedCoords.map(function(pt) {
            return pt.x + ',' + pt.y;
        }).join(',');
    }
    


    getSavedActiveRow().find('[name^="coords"]').val(newCoords)

    resetSelectedCoords();
    updateSavedCoordsArea(newCoords, areaID);

    // getSavedActiveRow()
    
    // let newCoords = nodes.map(function(pt) {
    //     console.log(pt.attr('x'));
        
    // });
    
    
    // Memperbarui koordinat berdasarkan posisi node yang di-drag
    // const x = position.left;
    // const y = position.top;

    // let _coords = getActiveRow().find('[name^="coords"]').val()
    // $("input[name='coords_"+nodeID+"']").val(x+","+y);
}

$("#savedFormContainer").on("click", "#saveArea", function(e){

    let areaID = $(this).attr("areaID");
    let alt = getSavedAltActivedRow();
    let shape = getSavedShapeActivedRow();
    let description = getSavedDescriptionActivedRow();
    let coords = getSavedCoordsActivedRow();
    let id_asset_group = getSavedIdAssetGroupActivedRow();
    let id_asset = getSavedAssetActivedRow();
    let status = getSavedStatusActivedRow()
    let device_type = '';
    let meta = '';

    let areaData = {
        alt: alt,
        shape: shape,
        description: description,
        coords: coords,
        id_asset_group: id_asset_group,
        id_asset: id_asset,
        status: status,
        device_type: device_type,
        meta: meta,
    };
    

    updateAreaApi(areaID, areaData);
})

$("#savedFormContainer").on("click", "#cancelSaveArea", function(e){
    console.log("cancelSaveArea()");
    
    let areaID = $(this).attr("areaID");
    let dataOrigin = $("#origindata_"+areaID);
    let alt = dataOrigin.attr("alt");
    let coordinate = dataOrigin.attr("coordinate");
    let shape = dataOrigin.attr("shape");
    let status = dataOrigin.attr("status");
    let meta = dataOrigin.attr("meta");
    let description = dataOrigin.attr("description");
    let device_type = dataOrigin.attr("device_type");
    let id_asset = dataOrigin.attr("id_asset");
    let id_asset_group = dataOrigin.attr("id_asset_group");    

    $(`#savedFormContainer #alt_${areaID}`).val(alt);
    $(`#savedFormContainer #coords_${areaID}`).val(coordinate);
    $(`#savedFormContainer #shape_${areaID}`).val(shape).change();
    $(`#savedFormContainer #status_${areaID}`).val(status).change();
    // $(`#savedFormContainer #meta_${areaID}`).val(meta);
    $(`#savedFormContainer #description_${areaID}`).text(description);
    // $(`#savedFormContainer #device_type_${areaID}`).text(device_type);
    $(`#savedFormContainer #id_asset_${areaID}`).text(id_asset);
    $(`#savedFormContainer #id_asset_group_${areaID}`).text(id_asset_group);


    updateSavedCoordsArea(coordinate, areaID);
    RowCoordUpdatedUpdated(areaID);
});

// function renderAreaFromNode(nodeID, size, position){
//     // Memperbaruin area pada peta berdasarkan ukuran dan posisi node yang di-resize
//     const width = size.width;
//     const height = size.height;
//     const x = position.left;
//     const y = position.top;

//     $("input[name='coords_"+nodeID+"']").val(x + "," + y + "," + (x + width) + "," + (y + height));
// }

// $(".node").draggable({
//     containment: "#map-image",
//     drag: function(event, ui){
//         updateNodeCoordinate(ui.helper);
//         // Update coordinate node sesuai dengan posisi baru
//     },
//     stop: function(event, ui){
//         // Lakukan resize area berdasarkan posisi akhir node
//         resizeArea(ui.helper);
//     }
// });

// function resizeArea(node){
//     const nodePosition = node.position;
//     const area_id = node.data('area-id'); // Dapatkan ID Area yang terhubung dengan node
//     const shape = node.data('shape'); // Dapatkan shape dari area (rectangle, triangle, polygon)

//     // if(shape === 'rectangle'){
        
//     // }else if(shape === 'triangle'){

//     // }else if(shape === 'polygon'){

//     // }

//     // Setelah update, perbarui tampilan mapster
//     $('#map-image').mapster('rebind', true);
// }

function resetPointClick(){
    pointClick = 0;
}

function resetSelectedCoords(){
    selectedCoords = [];
}

function resetAreaCount(){
    areaCount = 0;
}

function addSelectedCoords(x, y){
    selectedCoords.push({
        x: x,
        y: y
    });
}

function getCheckedRadio(){
    return $('#form-container input[type="radio"]:checked');
}

function getCheckedRadioValue(){
    return getCheckedRadio().val();
}

function getActiveRow(){
    return getCheckedRadio().closest('.area-row');
}

function getShapeActivedRow(){
    return getActiveRow().find('select[name^="shape_"]').val();
}

function getCoordsActivedRow(){
    return getActiveRow().find('select[name^="coords_"]').val();
}

function getAltActivedRow(){
    return getActiveRow().find('input[name^="alt"]').val();
}

function getStatusActivedRow(){
    return getActiveRow().find('select[name^="status"]').val();
}

function getAssetActivedRow(){
    return getActiveRow().find('select[name^="asset"]').val();
}

function getDescriptionActivedRow(){
    return getActiveRow().find('textarea[name^="description"]').val();
}

function getSavedCheckedRadio(){
    return $('#savedFormContainer input[type="radio"]:checked');
}

function getSavedCheckedRadioValue(){
    return getSavedCheckedRadio().val();
}

function getSavedActiveRow(){
    return getSavedCheckedRadio().closest('.area-row');
}

function getSavedShapeActivedRow(){
    return getSavedActiveRow().find('select[name^="shape_"]').val();
}

function getSavedCoordsActivedRow(){
    return getSavedActiveRow().find('input[name^="coords_"]').val();
}

function getSavedIdAssetGroupActivedRow(){
    return getSavedActiveRow().find('input[name^="id_asset_group_"]').val();
}

function getSavedAltActivedRow(){
    return getSavedActiveRow().find('input[name^="alt"]').val();
}

function getSavedStatusActivedRow(){
    return getSavedActiveRow().find('select[name^="status"]').val();
}

function getSavedAssetActivedRow(){
    return getSavedActiveRow().find('select[name^="asset"]').val();
}

function getSavedDescriptionActivedRow(){
    return getSavedActiveRow().find('textarea[name^="description"]').val();
}

function getImageOriginalSize(){
    // Dimensi element gambar #map-image
    let imgElement = document.getElementById('map-image');
    
    // Mendapatkan dimensi asli gambar
    let originalWidth = imgElement.naturalWidth;
    let originalHeight = imgElement.naturalHeight;

    return {
        originalWidth: originalWidth,
        originalHeight: originalHeight,
    };
}

function getImageDisplayedSize(){
    // Dimensi tampilan gambar
    let displayedWidth = $("#map-image").width();
    let displayedHeight = $("#map-image").height();

    return {
        displayedWidth: displayedWidth,
        displayedHeight: displayedHeight
    };
}

function getImageScale(){
    let origin = getImageOriginalSize();
    let displayed = getImageDisplayedSize();

    // Hitung skala
    scaleX = origin.originalWidth / displayed.displayedWidth;
    scaleY = origin.originalHeight / displayed.displayedHeight;

    return {
        scaleX: scaleX,
        scaleY: scaleY,
    };
}

function updateCoordsArea(_coords, areaID){
    let alt = getAltActivedRow();
    let shape = getShapeActivedRow();
    let status = getStatusActivedRow();
    let description = getDescriptionActivedRow();

    let newArea = {
        alt: alt,
        shape: shape,
        status: status,
        description: description,
        areaId: areaID,
        coords: _coords
    };

    updateAreaOnMap(newAreaName, newArea);
}

function updateSavedCoordsArea(_coords, areaID){
    
    let alt = getSavedAltActivedRow();
    let shape = getSavedShapeActivedRow();
    let status = getSavedStatusActivedRow();
    let description = getSavedDescriptionActivedRow();

    let newArea = {
        alt: alt,
        shape: shape,
        status: status,
        description: description,
        areaId: areaID,
        coords: _coords
    };

    console.log(newArea);
    
    updateAreaOnMap(savedAreaName, newArea);
}

function savedRowCoordUpdated(areaID){
    $(`#savedFormContainer #buttonArea_${areaID} #deleteArea`).addClass(`d-none`);
    $(`#savedFormContainer #buttonArea_${areaID} #updateArea`).removeClass(`d-none`);
}

function RowCoordUpdatedUpdated(areaID){
    $(`#savedFormContainer #buttonArea_${areaID} #deleteArea`).removeClass(`d-none`);
    $(`#savedFormContainer #buttonArea_${areaID} #updateArea`).addClass(`d-none`);
    $(`#savedFormContainer #savedRadio_${areaID}`).prop("checked", false);
}

function radiusCalc(x, y){
    return Math.sqrt(Math.pow(x - xOne, 2) + Math.pow(y - yOne, 2));
}

function updateCheckedRadioCoord(coords){
    getCheckedRadio().length ? getCheckedRadio().find('[name^="coords"]').val(coords) : alert('Pilih area yang akan ditambahkan koordinat.');
}

function removeArea(areaID){
    $("#areaContainer #newArea_"+areaID).remove();
    let nodes = $("#nodeContainer").find("[id*='newNode_"+areaID+"_']");
    nodes.map(function(index, element){
        element.remove();
    });
    $("#area-row-"+areaID).remove();
    renderArea();
}

function storeSavedUpdate(){
    console.log("storeSavedUpdate");
    
}

function setNodeCoordinate(areaID){
    //
    let nodes = $("#nodeContainer").find("[id*='savedNode_"+areaID+"_']");
}

</script>
</body>

</html>
