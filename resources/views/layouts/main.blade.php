<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Image Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.0/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imagemapster/1.8.0/jquery.imagemapster.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.js"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/imagemaper.js"></script>

    <style>
        .area-row {
            margin-bottom: 10px;
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
    </style>
</head>

<body>
    <div id="main-container" class="container my-5">
        @yield('container')
    </div>
</body>

</html>
