<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <title>Stock History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        @if ($stockData && $symbol)
            <h1 class="text-center"> {{ $symbol }} Historical data</h1>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <canvas id="myChart" width="800" height="400"></canvas>

                </div>
            </div>
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="table-responsive-xl">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Open</th>
                                    <th scope="col">High</th>
                                    <th scope="col">Low</th>
                                    <th scope="col">Close</th>
                                    <th scope="col">Volume</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockData as $stock)
                                    <tr>
                                        <th scope="row">{{ $loop->index + 1 }}</th>
                                        <td>{{ array_key_exists('date', $stock) ? date('Y-m-d', $stock['date']) : '' }}
                                        </td>
                                        <td>{{ array_key_exists('open', $stock) ? $stock['open'] : '' }}</td>
                                        <td>{{ array_key_exists('high', $stock) ? $stock['high'] : '' }}</td>
                                        <td>{{ array_key_exists('low', $stock) ? $stock['low'] : '' }}</td>
                                        <td>{{ array_key_exists('close', $stock) ? $stock['close'] : '' }}</td>
                                        <td>{{ array_key_exists('volume', $stock) ? $stock['volume'] : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

            <!-- JavaScript Bundle with Popper -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"
                integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script type="module">
                var stockData = @json($stockData);
                stockData = Object.values(stockData);

                const ctx = $("#myChart");
                const myChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: stockData.map((data) => {
                            let date = new Date(data.date * 1000);
                            return `${date.getFullYear()}-${
                                date.getMonth() + 1
                            }-${date.getDate()}`;
                        }),
                        datasets: [
                            {
                                label: "Open prices",
                                data: stockData.map((data) => data.open),
                                fill: false,
                                borderColor: "rgb(75, 192, 192)",
                                tension: 0.1,
                            },
                            {
                                label: "Close prices",
                                data: stockData.map((data) => data.close),
                                fill: false,
                                borderColor: "rgba(255, 99, 132, 0.2)",
                                tension: 0.1,
                            },
                        ],
                    },
                });
            </script>
        @else
            <h4> No stock for the given date range </h4>
            <p> From {{ $startDate }} to {{ $endDate }}</p>
            <a href="/"> Click here to return to form</a>
        @endif
    </div>



</body>

</html>
