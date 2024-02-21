<?php

namespace App\Http\Controllers;

use App\Models\MahasiswaModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class MahasiswaController extends Controller
{
  private MahasiswaModel $mahasiswaModel;

  public function __construct(MahasiswaModel $mahasiswaModel)
  {
    $this->mahasiswaModel = $mahasiswaModel;
  }

  public function index(Request $request): Response|RedirectResponse
  {
    $model['title'] = 'List Mahasiswa';
    $model['tableHeader'] = ['No', 'Nama', 'Jenis Kelamin', 'Alamat', 'Jumlah<br>Mata Kuliah', 'Aksi'];
    if ($request->input('search')) {
      $model['search'] = $request->input('search');
    }
    return response()->view('content.mahasiswa.list', $model);
  }

  public function list(Request $request): JsonResponse|RedirectResponse
  {
    $search = $request->input('search.value');
    $length = $request->input('length');
    $offset = $request->input('start');
    $response = [];
    return response()->json($response);
  }

  public function add(Request $request): Response|RedirectResponse
  {
    $model['title'] = 'List Mahasiswa';
    return response()->view('content.mahasiswa.add', $model);
  }

  public function edit(Request $request): Response|RedirectResponse
  {
    $model['title'] = 'Form Edit Mahasiswa';
    return response()->view('content.mahasiswa.edit', $model);
  }
}
