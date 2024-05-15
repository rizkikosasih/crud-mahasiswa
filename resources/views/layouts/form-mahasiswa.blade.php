<form action="{{ url('_' . $type) }}" id="form-validation" class="form-validation needs-validation" method="post" enctype="multipart/form-data" novalidate>
  @csrf
  @isset($mhs->id)
    <input type="hidden" name="id" value="{{ $mhs->id }}" autocomplete="off">
  @endisset
  <div class="mb-3">
    <label for="nama" class="form-label">Nama</label>
    <input type="text" name="nama" id="nama" class="form-control" value="{{ isset($mhs->nama) ? $mhs->nama : '' }}" placeholder="Nama Mahasiswa" required>
  </div>
  <div class="mb-3">
    <label for="alamat" class="form-label">Alamat</label>
    <textarea type="text" name="alamat" id="alamat" class="form-control" cols="3" placeholder="Alamat" required>{{ isset($mhs->alamat) ? $mhs->alamat : '' }}</textarea>
  </div>
  <div class="mb-3">
    <label for="jenis_kelamin" class="form-label d-block">Jenis Kelamin</label>
    @foreach ($jenis_kelamin as $jk)
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="jenis_kelamin" id="{{ $jk['value'] }}" value="{{ $jk['value'] }}" {{ $jk['checked'] ? 'checked' : 'required' }}>
        <label class="form-check-label" for="{{ $jk['value'] }}">{{ $jk['name'] }}</label>
      </div>
    @endforeach
  </div>
  <div class="mb-3">
    <h5 class="fw-semibold">List Mata Kuliah</h5>
    <hr class="my-1">
  </div>
  <div class="mb-3">
    <table class="table table-bordered table-hover">
      <thead>
        <th class="align-middle text-center">No</th>
        <th class="align-middle text-center">Mata Kuliah</th>
        <th class="align-middle text-center">Aksi</th>
      </thead>
      <tbody class="matkul-list">
        @if($mhsToMatkul)
          @foreach ($mhsToMatkul as $mtm)
            <tr class="matkul-item">
              <td class="text-center" id="number">{{ $loop->iteration }}</td>
              <td>
                <input
                  type="text"
                  name="matkul[]"
                  id="matkul"
                  class="form-control matkul"
                  value="{{ $mtm->nama_matkul }}"
                  required
                />
              </td>
              <td class="text-center">
                <div class="btn-group btn-group-sm" id="action-mation">
                  @if ($loop->iteration === $loop->count)
                    <button type="button" class="btn btn-primary add-matkul">Tambah</button>
                  @else
                    <button type="button" class="btn btn-danger delete-matkul">Hapus</button>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        @else
          <tr class="matkul-item">
            <td class="text-center" id="number">1</td>
            <td>
              <input
                type="text"
                name="matkul[]"
                id="matkul"
                class="form-control"
                value=""
                required
              />
            </td>
            <td class="text-center">
              <div class="btn-group btn-group-sm" id="action-matkul">
                <button type="button" class="btn btn-primary add-matkul">Tambah</button>
              </div>
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
  <div class="form-image mb-3">
    <label for="file_krs" class="form-label">
      <div>File KRS</div>
      <div class="text-muted">* file max size 1mb & extension (jpg, jpeg, png)</div>
    </label>
    <div class="custom-file">
      <input type="file" class="form-control image" placeholder="File KRS" name="file_krs"/>
    </div>
    <div class="border text-center p-3">
      <img
        src="{{ isset($mhs->file_krs) && $mhs->file_krs ? asset('assets/img/file_krs/' . $mhs->file_krs) : asset('assets/img/svg/no-image.svg') }}"
        class="img-fluid rounded bg-light preview-image"
        width="150"
        loading="lazy"
        onerror="this.onerror=null;this.src='{{ asset('assets/img/svg/no-image.svg') }}'"
      >
    </div>
  </div>
  <div class="d-flex justify-content-end gap-3" id="form-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
  </div>
</form>
