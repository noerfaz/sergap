<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ url('/storage/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ url('/storage/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ url('/storage/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ url('/storage/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ url('/storage/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ url('/storage/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ url('/storage/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ url('/storage/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/storage/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('/storage/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/storage/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ url('/storage/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/storage/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ url('/storage/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ url('/storage/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('backend/sneat-1.0.0/') }}/assets/vendor/css/core.css"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('backend/sneat-1.0.0/') }}/assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet"
        href="{{ asset('backend/sneat-1.0.0/') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <title>{{ $data->label }}</title>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="container">
            <div class="row mt-5">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 text-center">
                    <h2 style="text-transform:uppercase; font-weight:bold">sergap</h2>
                    <h3>Sistem Pergerakan Narapidana</h3>
                    <div class="card">
                        <div class="card-body">
                            <form action="" id="scannerBox">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="scanner" id="idgelang"
                                        class="form-control form-control-lg"
                                        placeholder="Tempelkan gelang Anda pada alat scanner ...">
                                    <input type="hidden" id="idScanner" value="{{ $data->slug }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const idgelang = $('#idgelang');
        const idscanner = $('#idScanner');

        $(idgelang).focus().val('');

        $('#scannerBox').submit(function(e) {
            e.preventDefault();

            $.ajax({
                    type: 'POST',
                    url: '{{ route('scanner.checkin') }}',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        idGelang: $(idgelang).val(),
                        idScanner: $(idscanner).val(),
                    }
                })
                .done(function(msg) {
                    const data = msg.data;
                    Swal.fire({
                        title: 'Checkin berhasil',
                        text: `Nama : ${data.nama}`,
                        icon: 'success',
                        timer: 3000,
                        showCancelButton: false,
                        shoeConfirmButton: false,
                    });
                })
                .fail(function(err) {
                    console.log(err);
                });

            $(idgelang).focus().val('');
        })
    });
</script>

</html>
