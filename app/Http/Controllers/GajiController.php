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
        $data = GajiModel::with('karyawan')->select('t_gaji.*');

        if ($request->karyawan_id) {
            $data->where('karyawan_id', $request->karyawan_id);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama_karyawan', function ($row) {
                return $row->karyawan ? $row->karyawan->nama : '-';
            })
            ->editColumn('gaji_pokok', function($row) {
                return number_format($row->gaji_pokok ?? 0, 0, ',', '.');
            })
            ->editColumn('tunjangan', function($row) {
                return number_format($row->tunjangan ?? 0, 0, ',', '.');
            })
            ->editColumn('potongan', function($row) {
                return number_format($row->potongan ?? 0, 0, ',', '.');
            })
            ->addColumn('total_gaji', function($row) {
                $gaji_pokok = $row->gaji_pokok ?? 0;
                $tunjangan  = $row->tunjangan ?? 0;
                $potongan   = $row->potongan ?? 0;
                $total = $gaji_pokok + $tunjangan - $potongan;
                return number_format($total, 0, ',', '.');
            })
            ->editColumn('tanggal_transaksi', function($row) {
                return $row->tanggal_transaksi ? $row->tanggal_transaksi : '-';
            })
            ->addColumn('aksi', function($row) {
                $btn  = '<button onclick="modalAction(\'' . url('/gaji/' . $row->transaksi_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/gaji/' . $row->transaksi_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/gaji/' . $row->transaksi_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
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
                'gaji_pokok' => 'required|numeric|min:0',
                'tunjangan' => 'nullable|numeric|min:0',
                'potongan' => 'nullable|numeric|min:0',
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

            $tunjangan = $request->tunjangan ?? 0;
            $potongan  = $request->potongan ?? 0;

            $total_gaji = $request->gaji_pokok + $request->tunjangan - $request->potongan;

            GajiModel::create([
                'karyawan_id' => $request->karyawan_id,
                'tanggal_transaksi' => now(),
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $tunjangan,
                'potongan' => $potongan,
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
                // Validasi
                $rules = [
                    'karyawan_id' => 'required|integer|exists:m_karyawan,karyawan_id',
                    'gaji_pokok'  => 'required|numeric|min:0',
                    'tunjangan'   => 'nullable|numeric|min:0',
                    'potongan'    => 'nullable|numeric|min:0',
                    'total_gaji'  => 'nullable|numeric',
                    'keterangan'  => 'nullable|string',
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

                // Nilai default jika kosong
                $gaji_pokok = $request->gaji_pokok ?? 0;
                $tunjangan  = $request->tunjangan ?? 0;
                $potongan   = $request->potongan ?? 0;

                $total_gaji = $gaji_pokok + $tunjangan - $potongan;

                $gaji->update([
                    'karyawan_id' => $request->karyawan_id,
                    'gaji_pokok'  => $gaji_pokok,
                    'tunjangan'   => $tunjangan,
                    'potongan'    => $potongan,
                    'total_gaji'  => $total_gaji,
                    'keterangan'  => $request->keterangan,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data gaji berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
            }
        }

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
