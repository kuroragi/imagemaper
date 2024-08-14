var selectedCoords = [];
var newAreasToAdd = [];
var areasToAdd = [];
var areaCount = 0;
var pointClick = 0;
const rectNode = 2,
    circleNode = 2;
let isFirstClick = true;
let isFirstClickCircle = true;
let isFirstAreaAdd = true;
let isdblClicked = false;
let firstX, firstY, $newArea, xOne, yOne;
var timeoutId = 0;
const selectedAreas = new Set();

function addPoint(x, y, s) {
    var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
    var alt = selectedRadio.find('[name^="alt"]').val();
    var shape = selectedRadio.find('[name^="shape"]').val();
    var status = selectedRadio.find('[name^="status"]').val();
    var description = selectedRadio.find('[name^="description"]').val();
    if (s === 'rect') {
        selectedCoords.push({
            x: x,
            y: y
        });

        if (selectedCoords.length === rectNode) {
            var coords = selectedCoords.map(function(pt) {
                return pt.x + ',' + pt.y;
            }).join(',');

            
            if (selectedRadio.length) {
                selectedRadio.find('[name^="coords"]').val(coords);
            } else {
                alert('Please select an area to add coordinates.');
            }

            let areaId = selectedRadio.find('[id^="area_id"]').val();

            let newArea = {
                areaId: areaId,
                alt: alt,
                coords: coords,
                shape: shape,
                status: status,
                description: description,
            };

            pointClick = 0;
            selectedCoords = [];
            addArea(newArea);
        }
    } else if (s === 'circle') {
        if (isFirstClickCircle) {
            xOne = x;
            yOne = y;

            isFirstClickCircle = false;
        } else if (pointClick === circleNode && isFirstClickCircle === false) {
            const radius = Math.sqrt(Math.pow(x - xOne, 2) + Math.pow(y - yOne, 2));

            var coords = xOne + ',' + yOne + ',' + radius

            if (selectedRadio.length) {
                selectedRadio.find('[name^="coords"]').val(coords);
            } else {
                alert('Please select an area to add coordinates.');
            }

            let areaId = selectedRadio.find('[id^="area_id"]').val();

            let newArea = {
                areaId: areaId,
                alt: alt,
                coords: coords,
                shape: shape,
                status: status,
                description: description,
            };

            pointClick = 0;
            isFirstClickCircle = true;
            selectedCoords = [];
            addArea(newArea);
        }
    } else if (s === 'poly') {
        selectedCoords.push({
            x: x,
            y: y
        });
        if (selectedCoords.length >= 3 && isdblClicked === true) {
            
            var coords = selectedCoords.map(function(pt) {
                return pt.x + ',' + pt.y;
            }).join(',');

            if (selectedRadio.length) {
                selectedRadio.find('[name^="coords"]').val(coords);
            } else {
                alert('Please select an area to add coordinates.');
            }

            let areaId = selectedRadio.find('[id^="area_id"]').val();

            let newArea = {
                areaId: areaId,
                alt: alt,
                coords: coords,
                shape: shape,
                status: status,
                description: description,
            };

            isdblClicked = false;
            pointClick = 0;
            selectedCoords = [];
            addArea(newArea);
        }else{
            selectedCoords.push({
                x: x,
                y: y
            });
        }
    }
}

function addAreaRow() {
    areaCount++;
    var newRow = `
        <div class="area-row row">
            <input type="hidden" id="area_id" value="${areaCount}">
            <div class="col col-1 align-content-center text-center">
                <input type="radio" name="selected_area" value="${areaCount}" checked>
            </div>
            <input type="hidden" class="form-control" name="coords_${areaCount}" placeholder="Coordinates" readonly>
            <div class="col col-2 align-content-center text-center">
                <select class="form-control" name="shape_${areaCount}">
                    <option value="rect">Rectangle</option>
                    <option value="circle">Circle</option>
                    <option value="poly">Polygon</option>
                </select>
            </div>
            <div class="col col-2 align-content-center text-center">
                <select class="form-control" name="status_${areaCount}" id="status">
                    <option value="kosong">Kosong</option>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>
            <div class="col col-3 align-content-center text-center">
                <input type="text" class="form-control" name="alt_${areaCount}" id="alt" placeholder="Name Area" required>
            </div>
            <div class="col col-3 align-content-center text-center">
                <textarea class="form-control" name="description_${areaCount}" placeholder="Description"></textarea>
            </div>
        </div>
    `;
    $('#form-container').append(newRow);
}

function addArea(area) {
    
    $("#image-map #newArea_"+area.areaId) ? $("#image-map #newArea_"+area.areaId).remove() : '';

    updateMap(area);
    runCallout();
    updateArea();
}

function collectArea() {

    areasToAdd = [];
    $('.area-row').each(function() {
        // var formData = $(this).find('input, select, textarea').serializeArray();
        var alt = $(this).find('[name^="alt"]').val();
        var coords = $(this).find('[name^="coords"]').val();
        var shape = $(this).find('[name^="shape"]').val();
        var status = $(this).find('[name^="status"]').val();
        var description = $(this).find('[name^="description"]').val();

        // if (coords.split(',').length < 4) {
        //     alert('Please select points on the image.');
        //     return false;
        // }

        // if(alt == null || alt == ''){
        //     areasToAdd = [];
        //     alert('Nama Area Jangan Ada Yang Kosong.');
        //     return;
        // }

        var areaData = {
            alt: alt,
            coords: coords,
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
        updateMap(area);

        var newRow = `
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

    // updateArea();
}

function updateArea() {
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

            if (areaId.startsWith('areabutton')) {
                $('#infoPanel').addClass('show');
                $('#mainContainer').addClass('shifted');
                updateInfoPanel($(this));
            } else if (areaId.startsWith('newArea_')) {
                // Area dengan ID 'newArea_' tidak bisa dipilih
                return false; // Mencegah area untuk dipilih
            }

            return true; // Biarkan Mapster melakukan default action-nya
        },
        onConfigured: function() {
            $('#map-image').mapster('set', ['kosong,baik,rusak']);
        }
    });
}

function updateTable(area) {
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

function selectedRadioCheck(x, y, c, s) {
    // var shape = $('input[name^="shape_"]:checked').val();

    // var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
    // var shape = selectedRadio.find('select[name^="shape_"]').val();
    // console.log('enter ' + s + ' ' + pointClick.toString());
    if (s === 'rect' || s === 'circle') {
        var radius = 2;
        if (c <= radius) {
            addPoint(x, y, s);
        } else {
            pointClick = 0;
        }
    }else{
        addPoint(x, y, s);
    }
}

function updateCoords($element) {
    const left = parseInt($element.css('left'), 10);
    const top = parseInt($element.css('top'), 10);
    const width = $element.width();
    const height = $element.height();

    const coords = `${left},${top},${left + width},${top + height}`;
    // $('input[name="coords_' + $('input[name="selected_area"]:checked').val() + '"]').val(coords);
}

function updateMap(area) {
    var areaRow = `
    <area data-status="${area.status}" alt="${area.alt},${area.status}"
                title="${area.alt}" href="javascript:void(0);" coords="${area.coords}"
                shape="${area.shape}" id="newArea_${area.areaId}">
    `;
    $('#image-map').append(areaRow);
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
