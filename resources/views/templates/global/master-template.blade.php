<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('templates.global.master-head')
        <title>Malicious URL Scanner</title>
    </head>
    <body>
        <div id="app">
            <search-component></search-component>
         </div>
    </body>
</html>


