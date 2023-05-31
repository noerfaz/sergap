<table>
    <thead>
        <tr>
            <th width="3"><b>NO</b></th>
            <th width="12"><b>UUID</b></th>
            <th width="14"><b>LABEL</b></th>
            <th width="30"><b>KETERANGAN</b></th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $item)
        <tr>
            <td valign="top" align="left">{{ $no++ }}</td>
            <td valign="top">{{ $item->slug }}</td>
            <td valign="top">{{ $item->labe }}</td>
            <td valign="top">{{ $item->keterangan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
