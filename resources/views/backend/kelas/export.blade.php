<table>
    <thead>
        <tr>
            <th width="3"><b>NO</b></th>
            <th width="12"><b>KODE</b></th>
            <th width="14"><b>NOMOR SEL</b></th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $item)
        <tr>
            <td valign="top" align="left">{{ $no++ }}</td>
            <td valign="top">{{ $item->kode }}</td>
            <td valign="top">{{ $item->kelas }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
