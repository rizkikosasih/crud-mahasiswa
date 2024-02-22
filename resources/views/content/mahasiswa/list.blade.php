@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h4 class="card-title">{{ $title }}</h4>
    <button type="button" class="btn btn-success show-modal" data-path="modal" data-type="add" data-id="0">Tambah</button>
  </div>
  <div class="card-body">
    {{-- Table --}}
    <div class="px-0">
      <table id="mahasiswa" class="table table-bordered table-striped w-100">
        <thead>
          <tr class="table-light">
            @foreach ($tableHeader as $th)
              <th class="align-middle text-center">{!! $th !!}</th>
            @endforeach
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection
