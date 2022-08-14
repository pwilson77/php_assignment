<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
<div class="row justify-content-center mt-5">
    <p>From {{ $emailMsg }} </p>
    <div>
        <div>
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
