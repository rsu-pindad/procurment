<table>
    <thead>
        <tr>
            <th>Jenis Ajuan</th>
            <th>Total HPS</th>
            <th>Total HPS Nego</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($labels as $index => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ isset($hps[$index]) ? number_format($hps[$index] ?? 0, 0, ',', '.') : 0 }}</td>
                <td>{{ isset($hpsNego[$index]) ? number_format($hpsNego[$index] ?? 0, 0, ',', '.') : 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
