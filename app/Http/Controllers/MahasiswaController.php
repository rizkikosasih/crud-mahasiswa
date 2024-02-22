<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
  public function index(Request $request): Response|RedirectResponse
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

    $recordsMahasiswa = DB::table('mahasiswa')
      ->select('mahasiswa.*')
      ->leftJoin('mhs_to_matkul', 'mhs_to_matkul.mahasiswa_id', '=', 'mahasiswa.id')
      ->where(DB::raw(1), '=', '1')
      ->where('mahasiswa.nama', 'like', '%' . $search . '%')
      ->where('mahasiswa.alamat', 'like', '%' . $search . '%')
      ->where('mahasiswa.jenis_kelamin', 'like', '%' . $search . '%')
      ->groupBy('mahasiswa_id')
      ->get()
      ->count();

    /* GET MAHASISWA */
    $mahasiswa = DB::table('mahasiswa')
      ->select('mahasiswa.*', DB::raw('count(mhs_to_matkul.mahasiswa_id) as count_matkul'))
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
              <a class='btn btn-primary show-modal' href='#' data-path='modal' data-type='edit' data-id='$mhs->id'>Edit</a>
              <a class='btn btn-danger show-modal' href='#' data-path='modal' data-type='delete' data-id='$mhs->id'>Delete</a>
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
    $title = 'Form Tambah Mahasiswa';
    $body = 'Hello ' . $type;
    $footer = '';
    return response()->json([
      'title' => $title,
      'body' => $body,
      'footer' => $footer,
      'classDialog' => 'modal-xl',
    ]);
  }

  public function edit(Request $request): Response|RedirectResponse
  {
    $model['title'] = 'Form Edit Mahasiswa';
    return response()->view('content.mahasiswa.edit', $model);
  }
}
