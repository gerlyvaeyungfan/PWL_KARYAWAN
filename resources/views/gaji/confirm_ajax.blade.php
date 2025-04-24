@empty($gaji)
    <div id="modal-gaji" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data gaji yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/gaji') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/gaji/' . $gaji->transaksi_id . '/delete_ajax') }}" method="POST" id="form-delete-gaji">
        @csrf
        @method('DELETE')

        <div id="modal-gaji" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Data Gaji</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                        Apakah Anda yakin ingin menghapus data gaji berikut?
                    </div>

                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-4">ID Transaksi:</th>
                            <td class="col-8">{{ $gaji->transaksi_id }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Nama Karyawan:</th>
                            <td>{{ $gaji->karyawan->nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Tanggal Transaksi:</th>
                            <td>{{ \Carbon\Carbon::parse($gaji->tanggal_transaksi)->format('d-m-Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Gaji Pokok:</th>
                            <td>Rp{{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Tunjangan:</th>
                            <td>Rp{{ number_format($gaji->tunjangan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Potongan:</th>
                            <td>Rp{{ number_format($gaji->potongan, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $totalGaji = ($gaji->gaji_pokok ?? 0) + ($gaji->tunjangan ?? 0) - ($gaji->potongan ?? 0);
                        @endphp
                        <tr>
                            <th class="text-right">Total Gaji:</th>
                            <td><strong class="text-success">Rp{{ number_format($totalGaji, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-right">Keterangan:</th>
                            <td>{{ $gaji->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $("#form-delete-gaji").validate({
                rules: {},
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                if (typeof dataGaji !== 'undefined') {
                                    dataGaji.ajax.reload();
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Server',
                                text: 'Terjadi kesalahan pada server.'
                            });
                            console.error(xhr.responseText);
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
