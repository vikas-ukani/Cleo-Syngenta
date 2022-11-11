<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Syngenta">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Syngenta TYMIRIUM&trade;</title>
    <link rel="stylesheet" href="/css/app.css">

</head>
<body>
<div id="syngent-app-wrapper" class="antialiased">
    <router-view></router-view>
</div>
<script src="/js/app.js"></script>
</body>
</html>
