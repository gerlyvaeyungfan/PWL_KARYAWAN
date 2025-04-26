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
    <form action="{{ url('/gaji/' . $gaji->transaksi_id . '/update_ajax') }}" method="POST" id="form-edit-gaji">
        @csrf
        @method('PUT')

        <div id="modal-gaji" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Gaji</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Karyawan</label>
                        <select name="karyawan_id" class="form-control" required>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->karyawan_id }}" {{ $k->karyawan_id == $gaji->karyawan_id ? 'selected' : '' }}>
                                    {{ $k->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-karyawan_id" class="form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="datetime-local" name="tanggal_transaksi" value="{{ \Carbon\Carbon::parse($gaji->tanggal_transaksi)->format('Y-m-d\TH:i') }}" class="form-control" required>
                        <small id="error-tanggal_transaksi" class="form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Gaji Pokok</label>
                        <input type="number" name="gaji_pokok" value="{{ $gaji->gaji_pokok }}" class="form-control" required>
                        <small id="error-gaji_pokok" class="form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Tunjangan</label>
                        <input type="number" name="tunjangan" value="{{ $gaji->tunjangan }}" class="form-control" value="0" min="0">
                        <small id="error-tunjangan" class="form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Potongan</label>
                        <input type="number" name="potongan" value="{{ $gaji->potongan }}" class="form-control" value="0" min="0">
                        <small id="error-potongan" class="form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Total Gaji</label>
                        <input type="text" id="total_gaji" value="{{ $gaji->total_gaji }}" class="form-control font-weight-bold text-primary" readonly>
                        <input type="hidden" name="total_gaji" id="total_gaji_input" value="{{ $gaji->total_gaji }}">
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control">{{ $gaji->keterangan }}</textarea>
                        <small id="error-keterangan" class="form-text text-danger"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            function calculateTotalGaji() {
                let gaji_pokok = parseFloat($('[name="gaji_pokok"]').val()) || 0;
                let tunjangan = parseFloat($('[name="tunjangan"]').val()) || 0;
                let potongan = parseFloat($('[name="potongan"]').val()) || 0;
                let total = gaji_pokok + tunjangan - potongan;
                $('#total_gaji').val(total.toFixed(2));
                $('#total_gaji_input').val(total.toFixed(2));
            }
            calculateTotalGaji();

            $('[name="gaji_pokok"], [name="tunjangan"], [name="potongan"]').on('input', calculateTotalGaji);

            $("#form-edit-gaji").validate({
                rules: {
                    karyawan_id: { required: true },
                    tanggal_transaksi: { required: true },
                    gaji_pokok: { required: true, number: true, min: 0 },
                    tunjangan: { number: true, min: 0 },
                    potongan: { number: true, min: 0 }
                },
                submitHandler: function(form) {
                    $('.form-text.text-danger').text('');
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
                                $.each(response.msgField, function(field, message) {
                                    $('#error-' + field).text(message[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Server',
                                text: 'Terjadi kesalahan. Silakan coba lagi.'
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
