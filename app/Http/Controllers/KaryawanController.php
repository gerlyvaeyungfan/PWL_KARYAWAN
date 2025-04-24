<?php
namespace App\Http\Controllers;

use App\Models\KaryawanModel;
use App\Models\JabatanModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Karyawan',
            'list'  => ['Home', 'Karyawan']
        ];

        $page = (object) [
            'title' => 'Daftar karyawan yang terdaftar dalam sistem',
            'list'  => ['Home', 'Karyawan']
        ];

        $activeMenu = 'karyawan';

        $jabatan = JabatanModel::all();

        return view('karyawan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'jabatan' => $jabatan
        ]);}

    public function list(Request $request)
    {
        $karyawan = KaryawanModel::select('karyawan_id', 'nama', 'jabatan_id', 'alamat', 'telepon', 'email')
            ->with('jabatan');

        if ($request->jabatan_id) {
            $karyawan->where('jabatan_id', $request->jabatan_id);
        }

        return DataTables::of($karyawan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($karyawan) {
                $btn  = '<button onclick="modalAction(\'' . url('/karyawan/' . $karyawan->karyawan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/karyawan/' . $karyawan->karyawan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/karyawan/' . $karyawan->karyawan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $jabatan = JabatanModel::select('jabatan_id', 'nama_jabatan')->get();
        return view('karyawan.create_ajax')
            ->with('jabatan', $jabatan);
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama'        => 'required|string|max:100',
                'jabatan_id'  => 'required|integer',
                'alamat'      => 'required|string|max:255',
                'telepon'     => 'required|string|max:15',
                'email'       => 'nullable'
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $karyawan = KaryawanModel::create($request->all());
    
            if ($karyawan) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Data karyawan berhasil disimpan'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal menyimpan data karyawan'
                ]);
            }
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Permintaan bukan melalui AJAX'
            ]);
        }
    }

    public function edit_ajax(string $id)
    {
        $karyawan = KaryawanModel::find($id);
        $jabatan = JabatanModel::select('jabatan_id', 'nama_jabatan')->get();
        return view('karyawan.edit_ajax', ['karyawan' => $karyawan, 'jabatan' => $jabatan]);
    
    }

    public function update_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'nama'       => 'required|string|max:100',
            'jabatan_id' => 'required|integer',
            'alamat'     => 'required|string|max:255',
            'telepon'    => 'required|string|max:15',
            'email'      => 'nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        $karyawan = KaryawanModel::find($id);

        if ($karyawan) {
            $karyawan->update($request->except('karyawan_id')); // jangan ubah ID
            return response()->json([
                'status'  => true,
                'message' => 'Data karyawan berhasil diupdate'
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Data karyawan tidak ditemukan'
        ]);
    }

    return redirect('/');
}


    public function confirm_ajax(string $id)
    {
        $karyawan = KaryawanModel::find($id);
        return view('karyawan.confirm_ajax', ['karyawan' => $karyawan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $karyawan = KaryawanModel::find($id);
            if ($karyawan) {
                $karyawan->delete();
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