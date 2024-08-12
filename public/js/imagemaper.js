var selectedCoords = [];
var areasToAdd = [];
var areaCount = 0;
var pointClick = 0;
const rectNode = 2,
    circleNode = 2;
let isFirstClick = true;
let isFirstClickCircle = true;
let firstX, firstY, $newArea, xOne, yOne;

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
        }
    } else if (s === 'poly' && selectedCoords.length > 2) {
        if (selectedCoords.length >= 3 && confirm('Are you done selecting points for the polygon?')) {
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
        }
    }
}

function addAreaRow() {
    areaCount++;
    var newRow = `
        <div class="area-row">
            <input type="radio" name="selected_area" value="${areaCount}">
            <input type="text" class="form-control" name="alt_${areaCount}" id="alt" placeholder="Name Area" required>
            <input type="text" class="form-control" name="coords_${areaCount}" placeholder="Coordinates" readonly>
            <select class="form-control" name="shape_${areaCount}">
                <option value="rect">Rectangle</option>
                <option value="circle">Circle</option>
                <option value="poly">Polygon</option>
            </select>
            <select class="form-control" name="status_${areaCount}" id="status">
                <option value="kosong">Kosong</option>
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
            </select>
            <textarea class="form-control" name="description_${areaCount}" placeholder="Description"></textarea>
        </div>
    `;
    $('#form-container').append(newRow);
}

function addArea() {
    areasToAdd = [];
    $('.area-row').each(function() {
        var formData = $(this).find('input, select, textarea').serializeArray();
        var coords = $(this).find('[name^="coords"]').val();

        // if (coords.split(',').length < 4) {
        //     alert('Please select points on the image.');
        //     return false;
        // }

        var areaData = {
            alt: formData.find(input => input.name.startsWith('alt')).value,
            coords: coords,
            shape: formData.find(input => input.name.startsWith('shape')).value,
            status: formData.find(select => select.name.startsWith('status')).value,
            description: formData.find(textarea => textarea.name.startsWith('description')).value
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
        ],
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