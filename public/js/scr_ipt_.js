var selectedCoords = [];
var areasToAdd = [];
var areaCount = 0;

function addPoint(x, y) {
    selectedCoords.push({ x: x, y: y });
    if (selectedCoords.length === 2) {
        var coords = selectedCoords.map(function(pt) {
            return pt.x + ',' + pt.y;
        }).join(',');
        
        // Find the selected radio button
        var selectedRadio = $('#form-container input[type="radio"]:checked').closest('.area-row');
        if (selectedRadio.length) {
            selectedRadio.find('[name^="coords"]').val(coords);
        } else {
            alert('Please select an area to add coordinates.');
        }
        
        selectedCoords = []; // Reset after assigning coordinates
    }
}


function addAreaRow() {
    areaCount++;
    var newRow = `
        <div class="area-row">
            <input type="radio" name="selected_area" value="${areaCount}">
            <input type="text" name="alt_${areaCount}" placeholder="Alt Text" required>
            <input type="text" name="coords_${areaCount}" placeholder="Coordinates" readonly>
            <input type="text" name="shape_${areaCount}" placeholder="Shape" required>
            <select name="status_${areaCount}"><option value="kosong">Kosong</option><option value="baik">Digunakan Kondisi Baik</option><option value="rusak">Digunakan Kondisi Rusak</option></select>
            <textarea name="deskripsi_${areaCount}"></textarea>
        </div>
    `;
    $('#form-container').append(newRow);
}

function addArea() {
    areasToAdd = []; // Reset areasToAdd before collecting new areas
    $('.area-row').each(function() {
        var formData = $(this).find('input, select, textarea').serializeArray();
        var coords = $(this).find('[name^="coords"]').val();

        if (coords.split(',').length < 4) {
            alert('Please select two points on the image.');
            return false;
        }

        var areaData = {
            alt: formData.find(input => input.name.startsWith('alt')).value,
            // title: formData.find(input => input.name.startsWith('title')).value,
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

    // Tambahkan area ke peta dengan warna abu-abu
    areasToAdd.forEach(area => {
        updateMap(area);
    });

    alert('Areas collected. You can now save all areas.');
}

function saveAreas() {
    if (areasToAdd.length === 0) {
        alert('No areas to save.');
        return;
    }

    // Debugging log to check areasToAdd
    // console.log('Areas to add:', areasToAdd);

    $.ajax({
        url: '{{ route("save.areas") }}',
        type: 'POST',
        data: { 
            odp_id: '{{ $odp->id }}',
            areas: areasToAdd,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // alert(response.success);
            // console.log(response);
            
            location.reload();
            response.areas.forEach(function(area) {
                updateMap(area);
                updateTable(area);
            });
            areasToAdd = [];
        },
        error: function(e){
            console.log(e);
            
        }
        
        
    });
    // areasToAdd.forEach(function(area) {
    //             updateMap(area);
    //             updateTable(area);
    //         });
}

function updateMap(area, isNew = false) {
    let color = isNew ? '808080' : '00ff00';
    $('#image-map').append(`<area data-key="${area.status}" alt="${area.alt}" title="${area.alt}" href="javascript:void(0);" coords="${area.coords}" shape="${area.shape}" onclick="runFunction('${area.status}')">`)
    $('#map-image').mapster('set', true, {
        key: area.alt,
        fillColor: color, // Warna abu-abu
        fillOpacity: 0.5
    });
}

function updateTable(area) {
    $('#area-table-body').append(
        '<tr>' +
            '<td>' + area.alt + '</td>' +
            '<td></td>' +
            '<td>' + area.coords + '</td>' +
            '<td>' + area.shape + '</td>' +
        '</tr>'
    );
}

$(document).ready(function() {
    $('#map-image').on('click', function(e) {
        var offset = $(this).offset();
        var x = e.pageX - offset.left;
        var y = e.pageY - offset.top;
        addPoint(x, y);
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