<table>
    <thead>
    <tr>
        <th>MaTK</th>
        <th>TenDangNhap</th>
    </tr>
    </thead>
    <tbody>
    @foreach($TaiKhoans as $tk)
        <tr>
            <td>{{ $tk->MaTK }}</td>
            <td>{{ $tk->TenDangNhap }}</td>
        </tr>
    @endforeach
    </tbody>
</table>