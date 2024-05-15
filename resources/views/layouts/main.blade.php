<!DOCTYPE html>
<html lang="{{ env('DEFAULT_LOCALE') }}" data-appName={{ env('APP_NAME') }} data-path="{{ url('/') }}" data-bs-theme="{{ isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>
  <meta name="description" content="{{config('variable.appDescription')}}">
  <meta name="keywords" content="{{config('variable.appKeyword')}}">
  <meta name="author" content="{{config('variable.appAuthor')}}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
  <div class="container mt-3">
    @yield('content')
  </div>

  @include('layouts.modal')

  {{-- script --}}
  @vite(['resources/assets/js/app.js'])
</body>

</html>
