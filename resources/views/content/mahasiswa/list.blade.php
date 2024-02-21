@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card">
  <div class="card-header">
    <h4 class="card-title">{{ $title }}</h4>
  </div>
  <div class="card-body">
    {{-- Form Search --}}
    <form action="{{ url('list') }}" method="get" class="form-inline">
      <div class="row gx-3 align-items-center">
        <div class="col-auto">
          <label for="search">Kata Kunci</label>
          <div class="input-group">
            <input name="search" id="search" type="text" class="form-control" placeholder="Search" aria-label="search">
            <button class="btn btn-outline-secondary" type="submit">Submit</button>
            @isset($search)
              <a href="{{ url('list') }}" class="btn btn-outline-danger" type="button">Clear</a>
            @endisset
          </div>
          <label for="note" class="text-muted">* kata kunci berdasarkan nama, jenis kelamin, alamat</label>
        </div>
      </div>
    </form>

    {{-- Table --}}
    <div class="mt-4">
      <table id="mahasiswa" class="table table-bordered table-striped w-100">
        <thead>
          <tr class="table-light text-center">
            @foreach ($tableHeader as $th)
              <th class="align-middle">{!! $th !!}</th>
            @endforeach
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection
