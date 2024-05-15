@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="d-flex gap-3">
      <h4 class="card-title">{{ $title }}</h4>
      <button type="button" class="btn btn-outline-{{ $_COOKIE['theme'] === 'dark' ? 'light' : 'dark' }} bg-gradient btn-sm tooltips" title="Switch Theme" id="btn-theme">
        <i class="ti ti-{{ $_COOKIE['theme'] === 'dark' ? 'moon' : 'sun' }}"></i>
      </button>
    </div>
    <button type="button" class="btn btn-success show-modal" data-path="modal" data-type="add" data-id="0">Tambah</button>
  </div>

  <div class="card-body">
    {{-- Table --}}
    <div class="px-0">
      <table id="mahasiswa" class="table table-bordered table-hover table-striped w-100">
        <thead>
          <tr>
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
