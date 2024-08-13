var selectedCoords = [];
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

function addPoint(x, y, s) {
    if (s === 'rect') {
        selectedCoords.push({
            x: x,
            y: y
        });

        if (selectedCoords.length === rectNode) {
            var coords = selectedCoords.map(function(pt) {
                return pt.x + ',' + pt.y;
            }).join(',');

            var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
            if (selectedRadio.length) {
                selectedRadio.find('[name^="coords"]').val(coords);
            } else {
                alert('Please select an area to add coordinates.');
            }

            pointClick = 0;
            selectedCoords = [];
            runCallout();
        }
    } else if (s === 'circle') {
        if (isFirstClickCircle) {
            xOne = x;
            yOne = y;

            isFirstClickCircle = false;
        } else if (pointClick === circleNode && isFirstClickCircle === false) {
            const radius = Math.sqrt(Math.pow(x - xOne, 2) + Math.pow(y - yOne, 2));

            var coords = xOne + ',' + yOne + ',' + radius
            console.log(coords.toString());

            var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
            if (selectedRadio.length) {
                selectedRadio.find('[name^="coords"]').val(coords);
            } else {
                alert('Please select an area to add coordinates.');
            }

            pointClick = 0;
            isFirstClickCircle = true;
            selectedCoords = [];
            runCallout();
        }
    } else if (s === 'poly') {
        selectedCoords.push({
            x: x,
            y: y
        });
        if (selectedCoords.length >= 3 && isdblClicked === true) {
            console.log('selesai');
            
            var coords = selectedCoords.map(function(pt) {
                return pt.x + ',' + pt.y;
            }).join(',');

            var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
            if (selectedRadio.length) {
                selectedRadio.find('[name^="coords"]').val(coords);
            } else {
                alert('Please select an area to add coordinates.');
            }

            isdblClicked = false;
            pointClick = 0;
            selectedCoords = [];
            runCallout();
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
            <div class="col col-1 align-content-center text-center">
                <input type="radio" name="selected_area" value="${areaCount}">
            </div>
            <div class="col col-3 align-content-center text-center">
                <input type="text" class="form-control" name="alt_${areaCount}" id="alt" placeholder="Name Area" required>
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
                <textarea class="form-control" name="description_${areaCount}" placeholder="Description"></textarea>
            </div>
        </div>
    `;
    $('#form-container').append(newRow);
}

function addArea() {
    if(areasToAdd.length > 0){
        const area_table = document.getElementById("area-table-body");
        const map_area = document.getElementById("image-map");
        for (let i = 0; i < areasToAdd.length; i++) {
            area_table.removeChild(area_table.lastElementChild);
            map_area.removeChild(map_area.lastElementChild);
        }
    
        // updateArea();
    };

    areasToAdd = [];
    $('.area-row').each(function() {
        // var formData = $(this).find('input, select, textarea').serializeArray();
        var coords = $(this).find('[name^="coords"]').val();

        // if (coords.split(',').length < 4) {
        //     alert('Please select points on the image.');
        //     return false;
        // }

        var areaData = {
            alt: $(this).find('[name^="alt"]').val(),
            coords: coords,
            shape: $(this).find('[name^="shape"]').val(),
            status: $(this).find('[name^="status"]').val(),
            description: $(this).find('[name^="description"]').val()
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

    updateArea();

    alert('Areas collected. You can now save all areas.');
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
        ],// Tetap gunakan singleSelect
        onClick: function(e) {
            const key = e.key; // Ambil key area yang dipilih

            if (selectedAreas.has(key)) {
                selectedAreas.delete(key); // Hapus area dari pilihan jika sudah dipilih sebelumnya
            } else {
                selectedAreas.add(key); // Tambahkan area ke pilihan
            }

            // Reset semua area terlebih dahulu
            $('#map-image').mapster('deselect');

            // Set semua area yang ada di dalam selectedAreas
            selectedAreas.forEach(areaKey => {
                $('#map-image').mapster('set', true, areaKey);
            });

            return false; // Menghentikan event handler default dari mapster
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
    console.log('enter ' + s + ' ' + pointClick.toString());
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
                shape="${area.shape}">
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