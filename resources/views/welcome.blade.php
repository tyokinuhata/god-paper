<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>God Paper</title>
        <link rel="stylesheet" href="css/app.css">
        <style>
            * {
                height: 100%;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <div id="app">
                <app></app>
            </div>
        </div>
        <script type="text/javascript">
            window.Laravel = window.Laravel || {};
            window.Laravel.csrfToken = "{{csrf_token()}}";
        </script>
        <script src="js/app.js"></script>
    </body>
</html>