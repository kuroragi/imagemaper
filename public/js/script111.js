var selectedCoords = [];
var areasToAdd = [];
var areaCount = 0;
var pointClick = 0;

function addPoint(x, y, s) {
    selectedCoords.push({ x: x, y: y });
    console.log(s);
    

    if ((s === '_rect' || s === '_circle') && selectedCoords.length === 2) {
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
    } else if (s === '_poly' && selectedCoords.length > 2) {
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
            <input type="text" name="alt_${areaCount}" id="alt" placeholder="Alt Text" required>
            <input type="text" name="coords_${areaCount}" placeholder="Coordinates" readonly>
            <select name="shape_${areaCount}">
                <option value="_rect">Rectangle</option>
                <option value="_circle">Circle</option>
                <option value="_poly">Polygon</option>
            </select>
            <select name="status_${areaCount}" id="status">
                <option value="kosong">Kosong</option>
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
            </select>
            <textarea name="deskripsi_${areaCount}" placeholder="Description"></textarea>
        </div>
    `;
    $('#form-container').append(newRow);
}

function addArea() {
    areasToAdd = [];
    $('.area-row').each(function() {
        var formData = $(this).find('input, select, textarea').serializeArray();
        var coords = $(this).find('[name^="coords"]').val();

        if (coords.split(',').length < 4) {
            alert('Please select points on the image.');
            return false;
        }

        var areaData = {
            alt: formData.find(input => input.name.startsWith('alt')).value,
            coords: coords,
            shape: formData.find(input => input.name.startsWith('shape')).value,
            status: formData.find(select => select.name.startsWith('status')).value,
            deskripsi: formData.find(textarea => textarea.name.startsWith('deskripsi')).value
        };

        areasToAdd.push(areaData);
    });

    if (areasToAdd.length === 0) {
        alert('No areas to add.');
        return;
    }

    areasToAdd.forEach(area => {
        updateMap(area, true);
    });

    alert('Areas collected. You can now save all areas.');
}

function saveAreas() {
    if (areasToAdd.length === 0) {
        alert('No areas to save.');
        return;
    }

    $.ajax({
        url: '/odpdetail', // Your save URL here
        type: 'POST',
        data: { 
            odp_id: '{{ $odp->id }}',
            areas: areasToAdd,
        },
        success: function(response) {
            console.log(response);
            
            // location.reload();
            // response.areas.forEach(function(area) {
            //     updateMap(area, false);
            //     updateTable(area);
            // });
            // areasToAdd = [];
        },
        error: function(e){
            console.log(e);
        }
    });
}

function updateMap(area, isNew = false) {
    let color = isNew ? '808080' : '00ff00';
    $('#image-map').append(`<area data-key="${area.status}" alt="${area.alt}" title="${area.alt}" href="javascript:void(0);" coords="${area.coords}" shape="${area.shape}" onclick="runFunction('${area.status}')">`)
    $('#map-image').mapster('set', {
        key: area.alt,
        fillColor: color,
        fillOpacity: 0.5
    });
}

function updateTable(area) {
    $('#area-table-body').append(
        '<tr>' +
            '<td><input type="radio" name="selected_area" value="' + area.alt + '"></td>' +
            '<td>' + area.alt + '</td>' +
            '<td>' + area.coords + '</td>' +
            '<td>' + area.shape + '</td>' +
            '<td>' + area.status + '</td>' +
            '<td>' + area.deskripsi + '</td>' +
        '</tr>'
    );
}

function selectedRadioCheck(x, y, c, s) {
    // var shape = $('input[name^="shape_"]:checked').val();
    
    // var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
    // var shape = selectedRadio.find('select[name^="shape_"]').val();
    console.log('enter '+ s + ' ' + pointClick.toString());
        if (s === '_rect' || s === '_circle'){
            var radius = 2;
            if(c <= radius){
                addPoint(x, y, s);
            }else{
                pointClick = 0;
            }
        }
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#map-image').on('click', function(e) {
        var offset = $(this).offset();
        var x = e.pageX - offset.left;
        var y = e.pageY - offset.top;
        pointClick++;
        var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
        var shape = selectedRadio.find('select[name^="shape_"]').val();
        // console.log(shape);
        
        
        selectedRadioCheck(x, y, pointClick, shape);
    });

    $('#add-area-btn').on('click', function() {
        addAreaRow();
    });

    $('#map-image').mapster({
        fillColor: 'ffff00',
        fillOpacity: 0.75,
        singleSelect: true,
        mapKey: 'alt',
        listKey: 'alt',
        areas: [
            {
                key: 'kosong',
                fillColor: 'd9d9d9',
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
            },
        ],
        onConfigured: function() {
            $('#map-image').mapster('set', ['kosong,baik,rusak']);
        }
    });
});
