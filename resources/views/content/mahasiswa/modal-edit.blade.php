@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card">
  <div class="card-header">
    <h4 class="card-title">{{ $title }}</h4>
  </div>
  <div class="card-body">
    <table class="table table-sm"></table>
  </div>
</div>
@endsection
