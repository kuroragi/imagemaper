const image = $('#map-image');
const areaContainer = $('#areaContainer')
const nodeContainer = $('#nodeContainer');
const selectedAreas = new Set();
const _newNode = 'newNode_';
const _newArea = 'newArea_';
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

function addPoint(x, y, shape, selectedRadio) {
    let alt = selectedRadio.find('[name^="alt"]').val();
    let status = selectedRadio.find('[name^="status"]').val();
    let description = selectedRadio.find('[name^="description"]').val();
    if (shape === 'rect') {
        addSelectedCoords(x, y);

        if (selectedCoords.length === rectNode) {
            let coords = selectedCoords.map(function(pt) {
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
            resetSelectedCoords();
            addArea(newArea);
        }
    } else if (shape === 'circle') {
        if (isFirstClickCircle) {
            xOne = x;
            yOne = y;

            isFirstClickCircle = false;
        } else if (pointClick === circleNode && isFirstClickCircle === false) {
            let coords = xOne + ',' + yOne + ',' + radiusCalc(x, y);

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
            resetSelectedCoords();
            addArea(newArea);
        }
    } else if (shape === 'poly') {
        addSelectedCoords(x, y);
        if (selectedCoords.length >= 3 && isdblClicked === true) {
            
            let coords = selectedCoords.map(function(pt) {
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
            resetPointClick();
            resetSelectedCoords();
            addArea(newArea);
        }else{
            addSelectedCoords(x, y);
        }
    }
}

function addAreaRow() {
    areaCount++;
    let newRow = `
        <div class="area-row row" id="area-row-${areaCount}">
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
            <div class="col col-1 align-content-center text-center">
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
            <div class="col col-1 align-content-center text-center">
                <button class="btn btn-danger" id="removeArea" areaID="${areaCount}"><i class="fa fa-circle-xmark"></i></button>
            </div>
        </div>
    `;
    $('#form-container').append(newRow);
}

function addArea(area) {
    
    $("#areaContainer #"+_newArea+area.areaId) ? $("#areaContainer #"+_newArea+area.areaId).remove() : '';

    $("#nodeContainer #new-node-"+area.areaId) ? $("#nodeContainer #new-node-"+area.areaId+"-"+pointClick).remove() : '';

    addMap(area);
    runCallout();
    updateArea();
}

function collectArea() {

    areasToAdd = [];
    $('.area-row').each(function() {
        // let formData = $(this).find('input, select, textarea').serializeArray();
        let alt = $(this).find('[name^="alt"]').val();
        let coords = $(this).find('[name^="coords"]').val();
        let shape = $(this).find('[name^="shape"]').val();
        let status = $(this).find('[name^="status"]').val();
        let description = $(this).find('[name^="description"]').val();

        // if (coords.split(',').length < 4) {
        //     alert('Please select points on the image.');
        //     return false;
        // }

        // if(alt == null || alt == ''){
        //     areasToAdd = [];
        //     alert('Nama Area Jangan Ada Yang Kosong.');
        //     return;
        // }

        let areaData = {
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
        addMap(area);

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

    // updateArea();
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
            group_id: groupId,
            areas: areasToAdd,
            // _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            // console.log(data);

            // alert(data);
            // data.areas.forEach(function(area) {
            //     updateTable(area);
            // });
            // areasToAdd = [];
            location.reload();
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}

$('.close-btn').click(function(){
    $('#infoPanel').removeClass('show');
    $('#mainContainer').removeClass('shifted');
    updateArea()
});

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

function updateCoords($element) {
    const left = parseInt($element.css('left'), 10);
    const top = parseInt($element.css('top'), 10);
    const width = $element.width();
    const height = $element.height();

    const coords = `${left},${top},${left + width},${top + height}`;
    // $('input[name="coords_' + $('input[name="selected_area"]:checked').val() + '"]').val(coords);
}

function addMap(area) {
    let areaRow = `
    <area data-status="${area.status}" alt="${area.alt},${area.status}"
                title="${area.alt}" href="javascript:void(0);" coords="${area.coords}"
                shape="${area.shape}" id="${_newArea}${area.areaId}">
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

function createNode(x, y, selectedRadio){


    let areaId = selectedRadio.val();

    const ID = _newNode+areaId+'_'+pointClick;

    // buat elemen node
    let node = $("<div id='"+ID+"' x='"+x+"'  y='"+y+"' class='node'></div>");

    console.log(x-5+', '+y-5);
    

    // Atur posisi node berdasarkan klik
    node.css({
        'top': (y - 5) + 'px',
        'left': (x - 5) + 'px',
    });

    $("#nodeContainer").append(node);

    createDraggableNode(ID, areaId);

}

function createDraggableNode(nodeID, areaID){
    $("#"+nodeID).draggable({
        containment: "#map-image-container", // Pastikan node tetap berada dalam batas gambar
        stop: function(event, ui){
            updateCoordsFromNode(areaID)
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

function updateCoordsFromNode(areaID){
    let areaShape = $("#areaContainer").find("[id*='newArea_"+areaID+"']").attr('shape');
    let nodes = $("#nodeContainer").find("[id*='newNode_"+areaID+"_']");

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
            let x = $(element).css('left').replace('px', '') * scaleX + 5;
            let y = $(element).css('top').replace('px', '') * scaleY + 5;
            addSelectedCoords(x, y);
        });
        
        newCoords = selectedCoords.map(function(pt) {
            return pt.x + ',' + pt.y;
        }).join(',');
    }


    getActiveRow().find('[name^="coords"]').val(newCoords)

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

// function updateAreaFromNode(nodeID, size, position){
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

function getActiveRow(){
    return $('#form-container input[type="radio"]:checked').closest('.area-row');
}

function updateCoordsArea(_coords, areaID){
    let alt = getActiveRow().find('[name^="alt"]').val();
    let shape = getActiveRow().find('[name^="shape"]').val();
    let status = getActiveRow().find('[name^="status"]').val();
    let description = getActiveRow().find('[name^="description"]').val();

    let newArea = {
        alt: alt,
        shape: shape,
        status: status,
        description: description,
        areaId: areaID,
        coords: _coords
    };

    addArea(newArea);
}

function radiusCalc(x, y){
    return Math.sqrt(Math.pow(x - xOne, 2) + Math.pow(y - yOne, 2));
}

function removeArea(areaID){
    $("#areaContainer #newArea_"+areaID).remove();
    let nodes = $("#nodeContainer").find("[id*='newNode_"+areaID+"_']");
    nodes.map(function(index, element){
        element.remove();
    });
    $("#area-row-"+areaID).remove();
    updateArea();
}