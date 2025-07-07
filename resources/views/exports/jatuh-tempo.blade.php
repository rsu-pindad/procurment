<table>
    <thead>
        <tr>
            <th>Status Jatuh Tempo</th>
            <th>Jumlah Ajuan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($labels as $index => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$index] ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
