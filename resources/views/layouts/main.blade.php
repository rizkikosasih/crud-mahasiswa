<!DOCTYPE html>
<html lang="{{ env('DEFAULT_LOCALE') }}" data-path="{{ url('/') }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>
  <meta name="description" content="{{config('variable.appDescription')}}">
  <meta name="keywords" content="{{config('variable.appKeyword')}}">
  <meta name="author" content="{{config('variable.appAuthor')}}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- style --}}
  @include('layouts.styles')
</head>

<body>
  <div class="container-fluid mt-3">
    @yield('content')
  </div>

  {{-- script --}}
  @include('layouts.scripts')
</body>

</html>
