@extends('layout.main')

@section('container')

<style>
        /* Style for radio buttons */
        .custom-radio {
            display: inline-block;
            cursor: pointer;
            font-size: 16px;
            margin-right: 15px;
        }

        .custom-radio input {
            display: none; /* Hide the actual radio button */
        }

        .custom-radio label {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        /* Change background color on hover */
        .custom-radio label:hover {
            background-color: #2980b9;
        }

        /* Style for checked radio button */
        .custom-radio input:checked + label {
            background-color: #2ecc71;
        }
    </style>

          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail Order</h1>
          </div>

            <!-- Nomor Katalog -->
             <!-- Nama Reagen -->
             <div class="form-group">
                <label for="namaReagen">Nomor Katalog</label>
                <input type="text" class="form-control" id="namaReagen" name="nameReagen" value="{{ $order->noCatalog }}" readonly>
            </div>

             <!-- Nama Reagen -->
             <div class="form-group">
                <label for="namaReagen">Nama Reagen</label>
                <input type="text" class="form-control" id="namaReagen" name="nameReagen" value="{{ $order->nameReagen }}" readonly>
            </div>

            <!-- Merk -->
            <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" class="form-control" id="merk" name="merk" value="{{ $order->merk }}" readonly>
            </div>

            <div class="form-group">
                <label for="packSize">Pack Size:</label>
                <input type="text" class="form-control" id="packSize" name="packSize" value="{{ $order->packSize }}" readonly>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $order->quantity }}" readonly>
            </div>

            <!-- Leadtime Order -->
            <div>
                <?php
                    if($order->status == 0){
                       echo "<p>Order created at: $order->created_at </p>
                        <p>Days passed since creation: $daysPassed </p>";
                    }else{
                        $created_at = $order->created_at; // Waktu pembuatan
                        $updated_at = $order->updated_at; // Waktu pembaruan

                        // Menghitung selisih waktu
                        $daysDifference = $updated_at->diffInDays($created_at);

                        // Menampilkan selisih waktu
                        echo "<p>Order created at: $order->created_at</p>
                        <p>Days passed since creation: $daysDifference </p>";
                    }
                ?>
                
                <!-- Tampilkan informasi lainnya dari objek $order sesuai kebutuhan -->
            </div>

        <form action="{{ route('order.update', $order->id) }}" method="post">
        @csrf
            <div>
                <label for="Status">Status:</label><br>
                <div class="custom-radio">
                    <input type="radio" name="status" id="inlineRadio1" value="0" {{ $order->status == 0 ? 'checked' : '' }}>
                    <label for="inlineRadio1">Not Complete</label>
                </div>

                <div class="custom-radio">
                    <input type="radio" name="status" id="inlineRadio2" value="1" {{ $order->status == 1 ? 'checked' : '' }}>
                    <label for="inlineRadio2">Complete</label>
                </div>
            </div>
            
            <?php
            if($order->status == 0){
                echo '<button type="submit" class="btn btn-primary">Submit</button>';
            }else{
                
            }
            ?>
        </form>

@endsection
