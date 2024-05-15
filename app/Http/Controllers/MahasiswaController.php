<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
  private string $krsPath = 'assets/img/file_krs';
  private array $trueExtension = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
  private int $trueSize = 1024 * 1024; //1mb

  public function index(): Response|RedirectResponse
  {
    $model['title'] = 'List Mahasiswa';
    $model['tableHeader'] = ['ID', 'Nama', 'Jenis Kelamin', 'Alamat', 'Jumlah<br>Mata Kuliah', 'Aksi'];
    return response()->view('content.mahasiswa.list', $model);
  }

  public function list(Request $request): JsonResponse|RedirectResponse
  {
    $search = $request->input('search.value');
    $length = $request->input('length');
    $offset = $request->input('start');
    $orderName = $request->input('order.0.name', 'mahasiswa.id');
    if ($orderName === 'count_matkul') {
      $orderName = DB::raw('count(mahasiswa_id)');
    }
    $orderDir = $request->input('order.0.dir', 'desc');
    $data = [];

    /* Records Mahasiswa */
    $recordsMahasiswa = DB::table('mahasiswa')
      ->where(DB::raw(1), '=', '1')
      ->where('nama', 'like', '%' . $search . '%')
      ->orWhere('alamat', 'like', '%' . $search . '%')
      ->orWhere('jenis_kelamin', 'like', '%' . $search . '%')
      ->get()
      ->count();

    /* GET MAHASISWA */
    $mahasiswa = DB::table('mahasiswa')
      ->select(['mahasiswa.*', DB::raw('count(mhs_to_matkul.mahasiswa_id) as count_matkul')])
      ->leftJoin('mhs_to_matkul', 'mhs_to_matkul.mahasiswa_id', '=', 'mahasiswa.id')
      ->where(DB::raw(1), '=', '1')
      ->where('mahasiswa.nama', 'like', '%' . $search . '%')
      ->orWhere('mahasiswa.alamat', 'like', '%' . $search . '%')
      ->orWhere('mahasiswa.jenis_kelamin', 'like', '%' . $search . '%')
      ->groupBy('mhs_to_matkul.mahasiswa_id')
      ->orderBy($orderName, $orderDir)
      ->limit($length)
      ->offset($offset)
      ->get();

    if ($mahasiswa) {
      foreach ($mahasiswa as $mhs) {
        $data[] = [
          '<div class="text-center">' . $mhs->id . '</div>',
          '<div class="text-start">' . $mhs->nama . '</div>',
          '<div class="text-start text-capitalize">' . $mhs->jenis_kelamin . '</div>',
          '<div class="text-start">' . $mhs->alamat . '</div>',
          '<div class="text-center">' . $mhs->count_matkul . '</div>',
          "<div class='text-center'>
            <div class='btn-group btn-group-sm'>
              <button class='btn btn-primary show-modal' data-path='modal' data-type='edit' data-id='$mhs->id'>Edit</button>
              <button class='btn btn-danger swal-confirm' data-path='delete/$mhs->id' data-message='Yakin mau dihapus?'>Delete</button>
            </div>
          </div>",
        ];
      }
    }

    return response()->json([
      'code' => 200,
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($recordsMahasiswa),
      'recordsFiltered' => intval($recordsMahasiswa),
      'data' => $data,
    ]);
  }

  public function modal(Request $request): JsonResponse|RedirectResponse
  {
    $type = $request->input('type');
    $id = intval($request->input('id'));
    $code = 200;

    switch ($type) {
      /* Add || Edit */
      case 'add':
      case 'edit':
        $title = 'Form Mahasiswa';
        $mhs = DB::table('mahasiswa')->where('id', '=', $id)->first();
        $mhsToMatkul = DB::table('mhs_to_matkul')->where('mahasiswa_id', '=', $id)->get();

        $model = [
          'type' => $type,
          'mhs' => !$id ? null : $mhs,
          'mhsToMatkul' => !$id ? null : $mhsToMatkul,
          'jenis_kelamin' => [
            [
              'name' => 'Pria',
              'value' => 'pria',
              'checked' => $id && $mhs->jenis_kelamin === 'pria',
            ],
            [
              'name' => 'Wanita',
              'value' => 'wanita',
              'checked' => $id && $mhs->jenis_kelamin === 'wanita',
            ],
          ],
        ];
        $body = view('layouts.form-mahasiswa', $model)->render();
        $footer = null;
        $option = ['backdrop' => false, 'keyboard' => false];
        $classDialog = null;
        break;

      /* Default Case */
      default:
        $title = 'Error';
        $body =
          '<div class="d-flex flex-column items-center text-center my-5"><h1 class="text-danger">405</h1><p>Method Not Allowed</p></div>';
        $footer = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        $option = null;
        $classDialog = 'modal-md';
        break;
    }
    return response()->json([
      'code' => $code,
      'title' => $title,
      'body' => $body,
      'footer' => $footer,
      'classDialog' => $classDialog,
      'option' => $option,
    ]);
  }

  public function _add(Request $request): JsonResponse
  {
    $code = 200;
    $message = null;
    $error = null;
    $fileUpload = $request->file('file_krs');
    $matkul = $request->input('matkul');

    DB::beginTransaction();
    try {
      /* Insert Mahasiswa */
      $mhsId = DB::table('mahasiswa')->insertGetId([
        'nama' => $request->input('nama'),
        'alamat' => $request->input('alamat'),
        'jenis_kelamin' => $request->input('jenis_kelamin'),
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      /* Insert Mahasiswa Matkul */
      if ($matkul) {
        foreach ($matkul as $m) {
          DB::table('mhs_to_matkul')->insert([
            'mahasiswa_id' => $mhsId,
            'nama_matkul' => $m,
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }
      }

      /* Upload File KRS */
      if ($fileUpload) {
        $fileName = $mhsId . '_' . $fileUpload->getClientOriginalName();
        $fileExtension = $fileUpload->getClientOriginalExtension();
        $fileSize = $fileUpload->getSize();
        if (in_array($fileExtension, $this->trueExtension)) {
          if ($fileSize <= $this->trueSize) {
            $fileUpload->move($this->krsPath, $fileName);
            DB::table('mahasiswa')
              ->where('id', '=', $mhsId)
              ->update(['file_krs' => $fileName]);
          } else {
            $code = 405;
            $error = 'File KRS too large';
            DB::rollBack();
          }
        } else {
          $code = 405;
          $error = 'Extension Not Valid';
          DB::rollBack();
        }
      }

      $message = $code === 200 ? 'Tambah Mahasiswa Berhasil' : null;
      DB::commit();
    } catch (\Exception $e) {
      $code = $e->getCode();
      $error = $e->getMessage();
      DB::rollBack();
    }

    return response()->json([
      'code' => $code,
      'message' => $message,
      'error' => $error,
    ]);
  }

  public function _edit(Request $request): JsonResponse
  {
    $code = 200;
    $message = null;
    $error = null;
    $fileUpload = $request->file('file_krs');
    $matkul = $request->input('matkul');

    DB::beginTransaction();
    $mhs = DB::table('mahasiswa')
      ->select(['id', 'file_krs'])
      ->where('id', '=', $request->input('id'))
      ->first();
    $mhsId = $mhs->id;

    try {
      if (!$mhsId) {
        $code = 405;
        $error = 'ID Tidak Ditemukan';
      } else {
        /* Update Mahasiswa */
        DB::table('mahasiswa')
          ->where('id', '=', $mhsId)
          ->update([
            'nama' => $request->input('nama'),
            'alamat' => $request->input('alamat'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'updated_at' => now(),
          ]);

        /* Delete Mahasiswa Matkul */
        DB::table('mhs_to_matkul')->where('mahasiswa_id', '=', $mhsId)->delete();

        /* Insert Mahasiswa Matkul */
        if ($matkul) {
          foreach ($matkul as $m) {
            DB::table('mhs_to_matkul')->insert([
              'mahasiswa_id' => $mhsId,
              'nama_matkul' => $m,
              'created_at' => now(),
              'updated_at' => now(),
            ]);
          }
        }

        /* Upload File KRS */
        if ($fileUpload) {
          $fileName = $mhsId . '_' . $fileUpload->getClientOriginalName();
          $fileExtension = $fileUpload->getClientOriginalExtension();
          $fileSize = $fileUpload->getSize();
          if (in_array($fileExtension, $this->trueExtension)) {
            if ($fileSize <= $this->trueSize) {
              if (file_exists($filePath = public_path($this->krsPath . '/' . $mhs->file_krs))) {
                File::delete($filePath);
              }

              $fileUpload->move($this->krsPath, $fileName);
              DB::table('mahasiswa')
                ->where('id', '=', $mhsId)
                ->update(['file_krs' => $fileName]);
            } else {
              $code = 405;
              $error = 'File KRS too large';
              DB::rollBack();
            }
          } else {
            $code = 405;
            $error = 'Extension Not Valid';
            DB::rollBack();
          }
        }

        $message = $code === 200 ? 'Ubah Mahasiswa Berhasil' : null;
        DB::commit();
      }
    } catch (\Exception $e) {
      $code = $e->getCode();
      $error = $e->getMessage();
      DB::rollBack();
    }

    return response()->json([
      'code' => $code,
      'message' => $message,
      'error' => $error,
    ]);
  }

  public function delete(Request $request, int $id): JsonResponse
  {
    $code = 200;
    $message = null;
    $error = null;

    DB::beginTransaction();
    $mhs = DB::table('mahasiswa')->where('id', '=', $id)->first();
    $mhsId = $mhs->id;

    try {
      if (!$mhsId) {
        $code = 405;
        $error = 'Hapus Mahasiswa Gagal';
      } else {
        /* Hapus Mahasiswa */
        DB::table('mahasiswa')->where('id', '=', $mhsId)->delete();

        /* Hapus Mahasiswa Matkul */
        DB::table('mhs_to_matkul')->where('mahasiswa_id', '=', $mhsId)->delete();

        /* Hapus File KRS */
        if (file_exists($filePath = public_path($this->krsPath . '/' . $mhs->file_krs))) {
          File::delete($filePath);
        }

        $message = 'Hapus Mahasiswa Berhasil';
        DB::commit();
      }
    } catch (\Exception $e) {
      $code = $e->getCode();
      $error = $e->getMessage();
      DB::rollBack();
    }

    return response()->json([
      'code' => $code,
      'message' => $message,
      'error' => $error,
    ]);
  }
}
