@extends('layout.main')

@section('container')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>



    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h3 class="">Reagen Detail</h3>

        <!-- button untuk edit -->
        <a href="{{ route('data.edit', ['noCatalog' => $data->noCatalog]) }}">
            <button class="btn btn-primary">Edit</button>
        </a>
    </div>

    <div class="container">
        <div class="row">
            <div class="col">
                <!-- nomor katalog -->
                <div>
                    <p>Catalog Number </p>
                </div>

                <!-- nama reagen -->
                <div class="mb-3">
                    <p>Reagen Name</p>
                </div>

                <!-- merk -->
                <div class="mb-3">
                    <p>Merk </p>
                </div>

                <!-- Pack Size -->
                <div class="mb-3">
                    <p>Pack Size</p>
                </div>
            </div>
            <div class="col">
                <!-- nilai nomor katalog -->
                <div>
                    <p>: {{ $data->noCatalog }}</p>
                </div>
                <!-- nilai nama reagen -->
                <div>
                    <p>: {{ $data->nameReagen }}</p>
                </div>
                <!-- nilai merk -->
                <div>
                    <p>: {{ $data->merk }}</p>
                </div>
                <!-- nilai pack size -->
                <div>
                    <p>: {{ $data->packSize }}</p>
                </div>
            </div>
            <div class="col">
                <!-- hazard symbol -->
                <div class="row">
                    <p>Hazard Symbol</p>
                </div>
                <!-- div kosong -->
                <div class="row mt-4">
                </div>
                <!-- MSDS -->
                <div class="row">
                    <p>MSDS</p>
                </div>
                <!-- harga -->
                <div class="row">
                    <p>Price</p>
                </div>
            </div>
            <!-- row 1 col 4 -->
            <div class="col">
                <div>
                    <!-- Hazard Symbol -->
                    <div class="custom-control custom-checkbox mb-3 row" id="checkboxContainer">

                        <!-- Toxic Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Toxic', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck1" name="hazardOptions[]" value="Toxic" {{ in_array('Toxic', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck1">
                                <img src="{{ asset('images/toxic.png') }}" alt="Gambar" width="50" height="50" class="customCheck1Image custom-control-image">
                            </label>
                        </div>

                        <!-- Corrosive Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Corrosive', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck2" name="hazardOptions[]" value="Corrosive" {{ in_array('Corrosive', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck2">
                                <img src="{{ asset('images/corrosive.png') }}" alt="Gambar" width="50" height="50" class="customCheck2Image custom-control-image">
                            </label>
                        </div>

                        <!-- Explosive Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Explosive', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck3" name="hazardOptions[]" value="Explosive" {{ in_array('Explosive', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck3">
                                <img src="{{ asset('images/explosive.png') }}" alt="Gambar" width="50" height="50" class="customCheck3Image custom-control-image">
                            </label>
                        </div>

                        <!-- Carcinogen Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Carcinogen', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck4" name="hazardOptions[]" value="Carcinogen" {{ in_array('Carcinogen', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck4">
                                <img src="{{ asset('images/carcinogen.png') }}" alt="Gambar" width="50" height="50" class="customCheck3Image custom-control-image">
                            </label>
                        </div>

                        <!-- Environment Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Environment', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck5" name="hazardOptions[]" value="Environment" {{ in_array('Environment', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck5">
                                <img src="{{ asset('images/Environmental-Hazard.png') }}" alt="Gambar" width="50" height="50" class="customCheck3Image custom-control-image">
                            </label>
                        </div>

                        <!-- Flammable Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Flammable', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck6" name="hazardOptions[]" value="Flammable" {{ in_array('Flammable', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck6">
                                <img src="{{ asset('images/flammable.png') }}" alt="Gambar" width="50" height="50" class="customCheck3Image custom-control-image">
                            </label>
                        </div>

                        <!-- Irritant Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Irritant', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck7" name="hazardOptions[]" value="Irritant" {{ in_array('Irritant', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck7">
                                <img src="{{ asset('images/irritant.png') }}" alt="Gambar" width="50" height="50" class="customCheck3Image custom-control-image">
                            </label>
                        </div>

                        <!-- Irritant Symbol -->
                        <div class="form-check form-check-inline" style="{{ !in_array('Oxidising', $hazardOptions) ? 'display: none;' : '' }}">
                            <input type="checkbox" class="custom-control-input custom-checkbox-input" id="customCheck8" name="hazardOptions[]" value="Oxidising" {{ in_array('Oxidising', $hazardOptions) ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="customCheck8">
                                <img src="{{ asset('images/oxidising.png') }}" alt="Gambar" width="50" height="50" class="customCheck3Image custom-control-image">
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <p>: <a href="{{ $data->msds }}">Link</a></p>
                </div>

                <div>
                    <p>: {{ $data->price }}</p>
                </div>
            </div><!-- row 1 col 4 -->
        </div>
    </div>

    <hr>

    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                <th scope="col">Date</th>
                <th scope="col">Batch Number</th>
                <th scope="col">Quantity</th>
                <th scope="col">Expired Date</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reagenIn as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}</td>
                    <td>{{ $item->batch }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->expiredDate)->format('d F Y') }}</td>
                    <td>
                        <!-- Tombol view label -->
                        <button type="button" class="btn btn-primary btn-show-modal" data-bs-toggle="modal" data-bs-target="#exampleModal" data-expired="{{ \Carbon\Carbon::parse($item->expiredDate)->format('d F Y') }}" data-id="{{ $item->Id }}">label
                        </button>
                        <!-- Form untuk delete stock -->
                        <form id="delete-form-{{ $item->Id }}" action="{{ route('management-stock.delete-stock', $item->Id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $item->Id }});">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tambahkan ini untuk pagination -->
        <div class="d-flex justify-content-center">
            {{ $reagenIn->links() }}
        </div>
        
    </div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">QR Code</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
      </div>
      <div class="modal-body" id="modalBody" style="background-color: white;">
        <div class="row border">
            <div class="col-md-3">
                <div id="qrcode" style="width: 180px; height: 180px;"></div>
            </div>
            <div class="col-md-9 label-reagen">
                <!-- kepala label -->
                <div class="row"> 
                    <div class="col-md-4 border"><img src="{{ asset('images/logo_b7.png') }}" alt="Logo" class="logo-b7"></div>
                    <div class="col border kepala-label">LABORATORIUM QC-ANDEV</div>
                </div>
                <!-- isi label -->
                <div class="row border">
                    <div class="col">
                        <div class="row">
                            <div class="col-md-4"><p>Nama Reagen </p></div><div class="col"><p>: {{ $data->nameReagen }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><p>Expired Date </p></div><div class="col"><p>: <span id="expired"></span></p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><p>Tanggal Buka </p></div><div class="col"><p>: </div>
                        </div>
                    </div>
                </div>
                <!-- catatan kaki label -->
                <div class="row border custom-right">
                    Distribution List - Lampiran 1:WI-QO-QC-1018.02
                </div>
            </div>
        </div>           
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="downloadBtn" class="btn btn-primary">Download</button>
      </div>
    </div>
  </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const buttons = document.querySelectorAll(".btn-show-modal");

        buttons.forEach(button => {
            button.addEventListener("click", function() {
                // const batch = this.getAttribute("data-batch");
                const expired = this.getAttribute("data-expired");
                const id = this.getAttribute("data-id");

                // document.getElementById("batch").textContent = batch;
                document.getElementById("expired").textContent = expired;

                generateQRCode("https://reagen.onexternal.com/qrcode/", id);
            });
        });
    });

    document.getElementById('downloadBtn').addEventListener('click', function() {
    // Ambil elemen HTML yang ingin diubah menjadi gambar
    var element = document.getElementById('modalBody');

    // Gunakan html2canvas untuk merender elemen menjadi gambar
    html2canvas(element, {
        background: '#ffffff', // Ubah latar belakang menjadi putih
        onrendered: function(canvas) {
            // Buat tautan unduhan untuk gambar
            var link = document.createElement('a');
            link.download = 'html_to_image.png';
            link.href = canvas.toDataURL();
            link.click();
        }
    });
    });

    function generateQRCode(text, id) {
    console.log(id);
    var typeNumber = 0; // Tipe nomor untuk QR code, 0 untuk otomatis
    var errorCorrectionLevel = 'L'; // Level koreksi kesalahan, bisa diganti dengan L, M, Q, atau H (dari yang terendah ke tertinggi)
    var qr = qrcode(typeNumber, errorCorrectionLevel);
    qr.addData(text + id); // Tambahkan data teks ke QR code
    qr.make(); // Buat QR code

    // Dapatkan tag gambar QR code
    var qrImage = qr.createImgTag();

    // Atur ukuran gambar QR code menggunakan properti width dan height
    // Nilai width dan height harus sesuai dengan ukuran yang diinginkan
    qrImage = qrImage.replace('<img ', '<img width="180px" height="180px" '); // Misalnya disini saya atur menjadi 200x200px

    // Tampilkan gambar QR code di dalam elemen div dengan id "qrcode"
    document.getElementById("qrcode").innerHTML = qrImage;
  }

  // Panggil fungsi generateQRCode dengan teks yang diinginkan
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

</script>

@endsection
