<?php

namespace App\Http\Controllers;

use App\Models\GajiModel;
use App\Models\KaryawanModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GajiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Gaji',
            'list'  => ['Home', 'Gaji']
        ];

        $page = (object) [
            'title' => 'Daftar transaksi gaji karyawan',
            'list'  => ['Home', 'Gaji']
        ];

        $activeMenu = 'gaji';

        $karyawan = KaryawanModel::all();

        return view('gaji.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'karyawan' => $karyawan
        ]);
    }

    public function getData()
    {
        $data = GajiModel::with('karyawan')->get(); // Mengambil data gaji beserta relasi karyawan

        // Mengembalikan data untuk DataTables, pastikan total_gaji ada di dalam data yang dikirim
        return datatables()->of($data)
            ->addColumn('total_gaji', function ($item) {
                return $item->gaji_pokok + $item->tunjangan - $item->potongan;
            })
            ->make(true);
    }

    public function list(Request $request)
    {
        $gaji = GajiModel::with('karyawan');

        if ($request->karyawan_id) {
            $gaji->where('karyawan_id', $request->karyawan_id);
        }

        return DataTables::of($gaji)
            ->addIndexColumn()
            ->addColumn('karyawan.nama', function ($gaji) {
                return $gaji->karyawan ? $gaji->karyawan->nama : '-';
            })
            ->addColumn('total_gaji', function ($gaji) {
                $total = $gaji->gaji_pokok + $gaji->tunjangan - $gaji->potongan;
                return number_format($total, 0, ',', '.'); // Format ribuan (optional)
            })
            ->addColumn('aksi', function ($gaji) {
                $btn  = '<button onclick="modalAction(\'' . url('/gaji/' . $gaji->transaksi_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/gaji/' . $gaji->transaksi_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/gaji/' . $gaji->transaksi_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $karyawan = KaryawanModel::select('karyawan_id', 'nama')->get();
        return view('gaji.create_ajax')
            ->with('karyawan', $karyawan);
    }

    public function store_ajax(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'required|integer|exists:m_karyawan,karyawan_id',
            'tanggal_transaksi' => 'required|date_format:Y-m-d\TH:i',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan' => 'required|numeric|min:0',
            'potongan' => 'required|numeric|min:0',
            'total_gaji',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal!',
                'msgField' => $validator->errors()
            ]);
        }

        $total_gaji = $request->gaji_pokok + $request->tunjangan - $request->potongan;
        $tanggal = Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_transaksi)->format('Y-m-d H:i:s');

        GajiModel::create([
            'karyawan_id' => $request->karyawan_id,
            'tanggal_transaksi' => $tanggal,
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan' => $request->tunjangan,
            'potongan' => $request->potongan,
            'total_gaji' => $total_gaji,  // Make sure to insert total_gaji
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data gaji berhasil disimpan!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ]);
    }
}


    public function edit_ajax(string $id)
    {
        $gaji = GajiModel::find($id);
        $karyawan = KaryawanModel::select('karyawan_id', 'nama')->get();
        return view('gaji.edit_ajax', ['gaji' => $gaji, 'karyawan' => $karyawan]);
    }


    public function update_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        try {
            // Validasi yang benar
            $rules = [
                'karyawan_id'       => 'required|integer|exists:m_karyawan,karyawan_id',
                'tanggal_transaksi' => 'required|date_format:Y-m-d\TH:i',
                'gaji_pokok'        => 'required|numeric|min:0',
                'tunjangan'         => 'required|numeric|min:0',
                'potongan'          => 'required|numeric|min:0',
                'total_gaji'        => 'required|numeric',
                'keterangan'        => 'nullable|string',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $gaji = GajiModel::find($id);
    
            if (!$gaji) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data gaji tidak ditemukan'
                ]);
            }
    
            $total_gaji = $request->gaji_pokok + $request->tunjangan - $request->potongan;
    
            $tanggal = Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_transaksi)->format('Y-m-d H:i:s');
            $gaji->update([
                'karyawan_id'       => $request->karyawan_id,
                'tanggal_transaksi' => $tanggal,
                'gaji_pokok'        => $request->gaji_pokok,
                'tunjangan'         => $request->tunjangan,
                'potongan'          => $request->potongan,
                'total_gaji'        => $total_gaji,
                'keterangan'        => $request->keterangan
            ]);
    
            return response()->json([
                'status'  => true,
                'message' => 'Data gaji berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }
    
    // Jika bukan AJAX
    return redirect('/');
}


    public function confirm_ajax(string $id)
    {
        $gaji = GajiModel::find($id);
        return view('gaji.confirm_ajax', ['gaji' => $gaji]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $gaji = GajiModel::find($id);
            if ($gaji) {
                $gaji->delete();
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return redirect('/');
    }
}
