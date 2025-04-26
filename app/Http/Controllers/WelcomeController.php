<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\JabaanModel;
use App\Models\KaryawanModel;

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        // Statistik Tambahan
        $totalKaryawan = DB::table('m_karyawan')->count();

        $totalGaji = DB::table('t_gaji')->sum(DB::raw('gaji_pokok + tunjangan - potongan'));

        $jabatanTerbanyak = DB::table('m_karyawan')
            ->select('jabatan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('jabatan_id')
            ->orderByDesc('total')
            ->first();

        $namaJabatan = optional(DB::table('m_jabatan')->where('jabatan_id', $jabatanTerbanyak->jabatan_id ?? null)->first())->nama_jabatan;

        // Ambil data untuk diagram pie
        $karyawanPerJabatan = DB::table('m_karyawan')
        ->join('m_jabatan', 'm_karyawan.jabatan_id', '=', 'm_jabatan.jabatan_id')
        ->select('m_jabatan.nama_jabatan', DB::raw('COUNT(*) as jumlah'))
        ->groupBy('m_jabatan.nama_jabatan')
        ->get();


        return view('welcome', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'totalKaryawan' => $totalKaryawan,
            'totalGaji' => $totalGaji,
            'jabatanTerbanyak' => $namaJabatan,
            'jumlahKaryawanJabatan' => $jabatanTerbanyak->total ?? 0,
            'karyawanPerJabatan' => $karyawanPerJabatan
        ]);
    }
}
