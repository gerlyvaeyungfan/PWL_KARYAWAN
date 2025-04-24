<?php

namespace App\Http\Controllers;

use App\Models\JabatanModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class JabatanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Jabatan',
            'list'  => ['Home', 'Jabatan']
        ];

        $page = (object) [
            'title' => 'Daftar jabatan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'jabatan';

        return view('jabatan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list()
    {
        $jabatan = JabatanModel::select('jabatan_id', 'nama_jabatan', 'keterangan');

        return DataTables::of($jabatan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($jabatan) {
                $btn  = '<button onclick="modalAction(\'' . url('/jabatan/' . $jabatan->jabatan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/jabatan/' . $jabatan->jabatan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/jabatan/' . $jabatan->jabatan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        return view('jabatan.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'nama_jabatan' => 'required|string|max:100|unique:m_jabatan,nama_jabatan',
                'keterangan'   => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            JabatanModel::create($request->all());

            return response()->json([
                'status'  => true,
                'message' => 'Data jabatan berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $jabatan = JabatanModel::find($id);
        return view('jabatan.edit_ajax', compact('jabatan'));
    }

    public function update_ajax(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_jabatan' => 'required|string|max:100|unique:m_jabatan,nama_jabatan,' . $id . ',jabatan_id',
            'keterangan'   => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        $jabatan = JabatanModel::find($id);
        if ($jabatan) {
            $jabatan->update([
                'nama_jabatan' => $request->nama_jabatan,
                'keterangan'   => $request->keterangan,
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil diupdate'
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }

    public function confirm_ajax(String $id)
    {
        $jabatan = JabatanModel::find($id);

        return view('jabatan.confirm_ajax', ['jabatan' => $jabatan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $jabatan = JabatanModel::find($id);
            if ($jabatan) {
                $jabatan->delete();
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data berhasil dihapus'
                ]);
            }
        }
        return redirect('/');
    }
}
