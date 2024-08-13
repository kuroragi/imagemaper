<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Image Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.0/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imagemapster/1.8.0/jquery.imagemapster.min.js"></script>

    
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
    </style>
</head>

<body>
    <div class="container my-5">
        @yield('container')
    </div>
</body>

</html>
