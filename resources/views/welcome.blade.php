@extends('layouts.template')

@section('content')

<div class="row mt-4">
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h4>{{ $totalKaryawan }}</h4>
                <p>Total Karyawan</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h4>Rp {{ number_format($totalGaji, 0, ',', '.') }}</h4>
                <p>Total Gaji Dibayarkan</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h4>{{ $jabatanTerbanyak ?? '-' }}</h4>
                <p>Jabatan Terbanyak ({{ $jumlahKaryawanJabatan }} Karyawan)</p>
            </div>
            <div class="icon">                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>
    <div class="card mt-4" style="margin-left: 7.5px">
        <div class="card-header">
            <h5 class="card-title text-center w-100">Diagram Karyawan per Jabatan</h5>
        </div>
        <div class="card-body">
            <canvas id="pieChart" height="100"></canvas>
        </div>
    </div>
    
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($karyawanPerJabatan->pluck('nama_jabatan')) !!},
            datasets: [{
                data: {!! json_encode($karyawanPerJabatan->pluck('jumlah')) !!},
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2',
                ],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false,
                }
            }
        }
    });
</script>
@endpush

@endsection