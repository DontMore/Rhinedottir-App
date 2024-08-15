@extends('layout.main')

@section('container')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- css -->
<style>
        .reagenSelectStyle {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
<!-- css -->

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

    <!-- Baris 2 -->
     <div class="row">
     <div class="card col-md-6">
      <div class="card-body">
        <div class="">
            <h2 class="center">Reagen In</h2>
            <select id="reagentSelect" class="reagenSelectStyle">
                <option value="">Select Reagent</option>
            </select>
            <canvas id="reagentChart" style="margin-top: 20px;"></canvas>
        </div>
      </div>
     </div>
     
     <div class="card col-md-6">
      <div class="card-body">
        <div class="">
            <h2 class="center">Reagen Out</h2>
              <select id="logbookReagentSelect" class="reagenSelectStyle">
                  <option value="">Select Reagent</option>
              </select>
              <canvas id="logbookReagentChart" style="margin-top: 20px;"></canvas>
        </div>
      </div>
     </div>
     </div>
     <!-- Baris 2 -->

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

    <script>
// chart reagen in
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

                // Create a gradient for the line
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(75, 192, 192, 0.6)');
                gradient.addColorStop(1, 'rgba(75, 192, 192, 0.1)');

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
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#ffffff',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: '#333',
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                cornerRadius: 4,
                                xPadding: 10,
                                yPadding: 10
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(200, 200, 200, 0.1)'
                                },
                                ticks: {
                                    color: '#666',
                                    font: {
                                        size: 12
                                    }
                                }
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


        // logbook reagen chart
        document.addEventListener("DOMContentLoaded", function() {
            let chartInstance = null;

            // Fetch and populate the dropdown
            fetch("{{ url('/logbook-reagent-list') }}")
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('logbookReagentSelect');
                    data.forEach(reagent => {
                        const option = document.createElement('option');
                        option.value = reagent.noCatalog;
                        option.text = reagent.nameReagen;
                        select.appendChild(option);
                    });
                });

            // Event listener for dropdown change
            $('#logbookReagentSelect').change(function() {
                const noCatalog = $(this).val();
                const nameReagen = $('#logbookReagentSelect option:selected').text();
                updateChart(noCatalog, nameReagen);
            });

            // Function to update the chart based on selected noCatalog
            function updateChart(noCatalog, nameReagen) {
                fetch(`{{ url('/logbook-chart-data') }}?noCatalog=${noCatalog}`)
                    .then(response => response.json())
                    .then(data => {
                        const labels = data.map(item => item.month);
                        const quantities = data.map(item => item.total_quantity);

                        const ctx = document.getElementById('logbookReagentChart').getContext('2d');

                        // Create a gradient for the line
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(75, 192, 192, 0.6)');
                        gradient.addColorStop(1, 'rgba(75, 192, 192, 0.1)');

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
                                    backgroundColor: gradient,
                                    borderWidth: 3,
                                    fill: true,
                                    pointRadius: 5,
                                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                                    pointHoverRadius: 8,
                                    pointHoverBackgroundColor: '#ffffff',
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        labels: {
                                            color: '#333',
                                            font: {
                                                size: 14
                                            }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                        cornerRadius: 4,
                                        xPadding: 10,
                                        yPadding: 10
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#666',
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(200, 200, 200, 0.1)'
                                        },
                                        ticks: {
                                            color: '#666',
                                            font: {
                                                size: 12
                                            }
                                        }
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
