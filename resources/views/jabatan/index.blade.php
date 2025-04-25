@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools"><!-- Tombol tambah menggunakan modal AJAX -->
            <button onclick="modalAction('{{ url('jabatan/create_ajax') }}')" 
                class="btn btn-sm btn-success mt-1">Tambah Jabatan
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Pesan sukses -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <!-- Pesan error -->
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Tabel daftar jabatan -->
        <table class="table table-bordered table-striped table-hover table-sm" id="table_jabatan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Jabatan</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal untuk AJAX form -->
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" 
data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
<script>
    // Fungsi untuk menampilkan modal
    function modalAction(url = ''){
        $('#myModal').load(url, function(){
            $('#myModal').modal('show');
        });
    }

    // Inisialisasi DataTables
    var dataJabatan;
    $(document).ready(function() {
        dataJabatan = $('#table_jabatan').DataTable({
            serverSide: true,
            ajax: {
                url: "{{ url('jabatan/list') }}",
                dataType: 'json',
                type: 'POST'
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "nama_jabatan", // Nama jabatan
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "keterangan", // Deskripsi atau info tambahan
                    className: "",
                    orderable: false,
                    searchable: true
                },
                {
                    data: "aksi", // Tombol edit/delete
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $('#jabatan_id').on('change', function () {
                dataJabatan.ajax.reload();
        });
    });
</script>
@endpush
