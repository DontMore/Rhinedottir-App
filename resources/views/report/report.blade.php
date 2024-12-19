@extends('layout.main')

@section('container')

<div class="col">
    <h2>Daftar Report</h2>
    <hr>
</div>

<div class="row">
    <!-- report logbook reagen -->
    <div class="col-lg-4">
        <div class="card card-margin">
            <div class="card-header no-border">
                <h5 class="card-title">Logbook Report</h5>
            </div>
            <div class="card-body pt-0">
                <div class="widget-49">
                        <span>laporan yang mencatat aktivitas atau kejadian penting dalam suatu periode untuk melacak progres, mendokumentasikan kegiatan, dan memastikan akuntabilitas.</span>
                    <div class="widget-49-meeting-action">
                        <a href="#" class="btn btn-sm btn-flash-border-primary">View Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- report historical reagen -->
    <div class="col-lg-4">
        <div class="card card-margin">
            <div class="card-header no-border">
                <h5 class="card-title">Historical Reagen Reaport</h5>
            </div>
            <div class="card-body pt-0">
                <div class="widget-49">
                        <span>laporan yang mencatat riwayat penggunaan, penerimaan, dan penyimpanan reagen dalam suatu periode untuk memantau dan menganalisis data secara historis.</span>
                    <div class="widget-49-meeting-action">
                        <a href="#" class="btn btn-sm btn-flash-border-primary">View Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- report daftar reagen -->
    <div class="col-lg-4">
            <div class="card card-margin">
                <div class="card-header no-border">
                    <h5 class="card-title">Reagen List Report</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="widget-49">
                            <span>laporan yang berisi daftar reagen yang tersedia, termasuk informasi seperti nomor katalog, nama, merck, dan packing siz, untuk memantau inventaris reagen secara terorganisir.</span>
                        <div class="widget-49-meeting-action">
                            <a href="#" class="btn btn-sm btn-flash-border-primary">View Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- report reagen expired -->
    <div class="col-lg-4">
            <div class="card card-margin">
                <div class="card-header no-border">
                    <h5 class="card-title">Reagen Expired Report</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="widget-49">
                            <span>laporan yang mencatat reagen yang telah melewati tanggal kedaluwarsa, untuk membantu memantau dan mengelola reagen yang perlu dibuang atau diganti.</span>
                        <div class="widget-49-meeting-action">
                            <a href="#" class="btn btn-sm btn-flash-border-primary">View Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection
