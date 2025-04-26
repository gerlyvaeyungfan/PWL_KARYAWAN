@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('gaji/create_ajax') }}')" 
                class="btn btn-sm btn-success mt-1">Tambah Gaji
            </button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_gaji">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan</th>
                    <th>Potongan</th>
                    <th>Total Gaji</th>
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
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var dataGaji;
    $(document).ready(function () {
        // Inisialisasi DataTables dengan AJAX
        dataGaji = $('#table_gaji').DataTable({
            serverSide: true,
            ajax: {
                url: "{{ url('gaji/list') }}",
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
                { data: "karyawan.nama" },
                { data: "tanggal_transaksi" },
                { data: "gaji_pokok", className: "text-right" },
                { data: "tunjangan", className: "text-right" },
                { data: "potongan", className: "text-right" },
                { data: "total_gaji", className: "text-right" },
                { data: "keterangan",
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "aksi",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Memperbarui DataTables saat filter karyawan diubah
        $('#karyawan_id').on('change', function () {
            dataGaji.ajax.reload();
        });
    });
</script>
@endpush
