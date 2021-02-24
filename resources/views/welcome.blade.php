<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=0">
        <title>Live Wire</title>
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
            <lovewire:styles/>
                </head>
                <body>
            <livewire:comments />
            <livewire:scripts/>    
    </body>
</html>
