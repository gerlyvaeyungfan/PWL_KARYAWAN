<!-- Form Tambah Gaji -->
<form action="{{ url('/gaji/ajax') }}" method="POST" id="form-tambah-gaji">
    @csrf
    <div id="modal-gaji" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Gaji</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id" class="form-control" required>
                        <option value="">- Pilih Karyawan -</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->karyawan_id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-karyawan_id" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Gaji Pokok</label>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control" required>
                    <small id="error-gaji_pokok" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tunjangan</label>
                    <input type="number" name="tunjangan" id="tunjangan" class="form-control" value="0" min="0">
                    <small id="error-tunjangan" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Potongan</label>
                    <input type="number" name="potongan" id="potongan" class="form-control" value="0" min="0">
                    <small id="error-potongan" class="error-text text-danger"></small>
                </div>
                
                <div class="form-group">
                    <label>Total Gaji</label>
                    <input type="text" id="total_gaji" class="form-control font-weight-bold text-primary" readonly>
                    <input type="hidden" name="total_gaji" id="total_gaji_input">
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                    <small id="error-keterangan" class="error-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<!-- Script -->
<script>
    $(document).ready(function() {

        // Hitung Total Gaji
        function calculateTotalGaji() {
            let gaji_pokok = parseFloat($('#gaji_pokok').val()) || 0;
            let tunjangan = parseFloat($('#tunjangan').val()) || 0;
            let potongan = parseFloat($('#potongan').val()) || 0;
            let total_gaji = Math.max(0, gaji_pokok + tunjangan - potongan); // Hindari negatif
            $('#total_gaji').val(total_gaji.toFixed(2));
        }

        // Trigger awal
        calculateTotalGaji();

        // Recalculate saat input berubah
        $('#gaji_pokok, #tunjangan, #potongan').on('input', calculateTotalGaji);

        // Reset saat modal ditutup
        $('#myModal').on('hidden.bs.modal', function () {
            $('#form-tambah-gaji')[0].reset();
            $('.error-text').text('');
            $('#total_gaji').val('');
            $('#total_gaji_input').val('');
            $('.form-control').removeClass('is-invalid');
        });

        // Setup CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Validasi dan Submit
        $("#form-tambah-gaji").validate({
            rules: {
                karyawan_id: { required: true },
                gaji_pokok: { required: true, number: true, min: 0 },
                tunjangan: { number: true, min: 0 },
                potongan: { number: true, min: 0 }
            },
            submitHandler: function(form) {
                calculateTotalGaji();
                $('#total_gaji_input').val($('#total_gaji').val());
                $('.error-text').text('');
                $.ajax({
                    url: form.action,
                    method: form.method,
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            if (typeof dataGaji !== 'undefined') {
                                dataGaji.ajax.reload(); // reload datatable
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
                            title: 'Server Error',
                            text: 'Terjadi kesalahan pada server. Silakan coba lagi.'
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
