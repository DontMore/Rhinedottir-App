@extends('layout.main')

@section('container')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <div class="container-fluid">
    <!-- baris 1 -->
    <div class="row baris-1">
      <!-- baris 1 kolom 1 -->
      <div class="card col m-1 reagen-varian">
        <div class="card-body text-center">
          <h2>{{ $totalReagen }}</h2>
          <p>Reagent Varian</p>
        </div>
      </div><!-- baris 1 kolom 1 -->

      <!-- baris 1 kolom 2 -->
      <div class="card col m-1 total-reagen">
        <div class="card-body text-center">
          <h2><h2>{{ $totalQuantity ?? 0 }}</h2></h2>
          <p>Total Reagent</p>
        </div>
      </div><!-- baris 1 kolom 2 -->

      <!-- baris 1 kolom 3 -->
      <div class="card col m-1 quantity-in">
        <div class="card-body text-center">
          <h2>{{ $totalQuantityIn ?? 0 }}</h2>
          <p>Reagent In</p>
        </div>
      </div><!-- baris 1 kolom 3 -->

      <!-- baris 1 kolom 4 -->
      <div class="card col m-1 reagen-out">
        <div class="card-body text-center">
          <h2>{{ $totalQuantityOut ?? 0 }}</h2>
          <p>Reagent Taken</p>
        </div>
      </div><!-- baris 1 kolom 4 -->
    </div> <!-- baris 1 -->

    <!-- Baris 3 -->
    <div class="row baris-3">

      <!-- baris 3 kolom 1 -->
      <div class="card col-md-3 m-1 p-0">
        <div class="card-body text-center">
          <p>Stock Kosong</p>
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th scope="col">Catalog Number</th>
                <th scope="col">Reagen Name</th>
                <th scope="col">Quantity</th>
              </tr>
            </thead>
            <tbody>
          @foreach($zeroStockReagen as $item)
              <tr>
                <td>{{ $item->noCatalog }}</td>
                <td>{{ $item->reagen->nameReagen }}</td>
                <td>{{ $item->quantity }}</td>
              </tr>
          @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- baris 3 kolom 4 -->

      <!-- baris 3 kolom 1 -->
      <div class="card col-md-3 m-1 p-0">
        <div class="card-body text-center">
          <p>Reagen Order</p>
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th scope="col">Catalog Number</th>
                <th scope="col">Reagen Name</th>
                <th scope="col">Quantity</th>
              </tr>
            </thead>
            <tbody>
          @foreach($reagenOrder as $order)
            <tr>
              <td>{{ $order->noCatalog }}</td>
              <td>{{ $order->nameReagen }}</td>
              <td>{{ $order->quantity }}</td>
            </tr>
          @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- baris 3 kolom 4 -->

      <!-- baris 3 kolom 1 -->
      <div class="card col m-1 p-0">
        <div class="card-body text-center">
          <p>Regent Expired</p>
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th scope="col">Catalog Number</th>
                <th scope="col">Reagen Name</th>
                <th scope="col">Quantity</th>
                <th scope="col">ExpireD Date</th>
              </tr>
            </thead>
            <tbody>
          @foreach($reagenED as $expired)
              <tr>
              <td>{{ $expired->noCatalog }}</td>
              <td>{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</td> <!-- Tambahkan pengecekan -->
              <td>{{ $expired->quantity }}</td>
              <td>{{ $expired->expiredDate}}</td>
              </tr>
          @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- baris 3 kolom 4 -->
    </div><!-- Baris 3 -->

    <div class="col-md-6">
        <label for="reagentSelect">Choose a Reagent:</label>
        <select id="reagentSelect">
            <option value="">Select Reagent</option>
        </select>
        <canvas id="reagentChart" style="margin-top: 20px;"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let chartInstance = null;

            // Fetch and populate the dropdown
            fetch("{{ url('/reagent-list') }}")
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('reagentSelect');
                    data.forEach(reagent => {
                        const option = document.createElement('option');
                        option.value = reagent.noCatalog;
                        option.text = reagent.nameReagen;
                        select.appendChild(option);
                    });
                });

            // Event listener for dropdown change
            $('#reagentSelect').change(function() {
                const noCatalog = $(this).val();
                const nameReagen = $('#reagentSelect option:selected').text();
                updateChart(noCatalog, nameReagen);
            });

            // Function to update the chart based on selected noCatalog
            function updateChart(noCatalog, nameReagen) {
                fetch(`{{ url('/chart-data') }}?noCatalog=${noCatalog}`)
                    .then(response => response.json())
                    .then(data => {
                        const labels = data.map(item => item.month);
                        const quantities = data.map(item => item.total_quantity);

                        const ctx = document.getElementById('reagentChart').getContext('2d');

                        // Destroy the previous chart instance if it exists
                        if (chartInstance !== null) {
                            chartInstance.destroy();
                        }

                        chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: nameReagen,
                                    data: quantities,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 2,
                                    fill: false
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Load chart for default or first selection
            updateChart('', '');
        });
    </script>

@endsection
