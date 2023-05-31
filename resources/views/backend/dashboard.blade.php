@extends('backend.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Halooo <b>{{ Auth::user()->nama }}</b> Selamat datang
                                    kembali ! 🎉</h5>
                                <p class="mb-4">
                                    Selamat beraktifitas & tetap semangat!
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('backend/sneat-1.0.0/') }}/assets/img/illustrations/man-with-laptop-light.png"
                                    height="140" alt="View Badge User"
                                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-5">
                <div class="card">
                    <h6 class="card-header">
                        <div class="row">
                            <div class="col-sm-7">
                                TEMPAT
                            </div>
                            <div class="col-sm-5">
                                <input type="text" class="form-control form-control-sm" id="tgl_chart">
                            </div>
                        </div>

                    </h6>
                    <div class="card-body">
                        <section id="box_chart"></section>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="card">
                    <h6 class="card-header">CHECKIN TERAKHIR</h6>
                    <div class="card-body">
                        <table class="table table-sm" id="table_checkin">
                            <thead>
                                <tr>
                                    <th>nama</th>
                                    <th>no gelang</th>
                                    <th>id napi</th>
                                    <th>scanner</th>
                                    <th>waktu</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.js">
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        allTanggal().then(function() {
            getGrafik();
        })

        function allTanggal() {
            return new Promise(function(resolve, reject) {
                $('#tgl_chart').flatpickr({
                    mode: 'range',
                    defaultDate: ['{{ date('Y-m-d') }}', '{{ date('Y-m-d') }}'],
                    onClose: function() {
                        getGrafik();
                    }
                });
                resolve();
            });
        }

        function getGrafik() {
            getChart();
            getKunjunganTable();
        }

        function getChart() {
            const box_chart = '#box_chart';
            const div_chart = '#div_chart';
            const tanggal = $('#tgl_chart').val();

            $.ajax({
                    type: 'POST',
                    url: '{{ route("chart-sel") }}',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        tanggal: tanggal,
                    },
                    beforeSend: function() {
                        $(box_chart).html('Loading...');
                    }
                })
                .done(function(msg) {
                    const data = msg.data;
                    $(box_chart).html(`<canvas id="${div_chart}" width="100%" height="60"></canvas>`);

                    var label = [];
                    var total = [];

                    $.each(data, function(i, val) {
                        label.push(val.label);
                        total.push(val.total);
                    })

                    gambarGrafik(label, total, div_chart, tanggal);
                })
                .fail(function(err) {
                    console.log(err);
                })
        }

        function getKunjunganTable() {
            const tanggalBox = $('#tgl_chart').val();

            let datatables = $('#table_checkin').DataTable({
                scrollX: true,
                responsive: true,
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: false,
                pageLength: 10,
                bDestroy: true,
                // order: [
                //     [5, 'desc']
                // ],
                ajax: {
                    url: "{{ route('chart-sel-napi') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = $("input[name=_token]").val();
                        d.tanggal = tanggalBox;
                    },
                },
                columns: [{
                        data: 'nama',
                    },
                    {
                        data: 'id_gelang',
                    },
                    {
                        data: 'id_napi',
                    },
                    {
                        data: 'label',
                    },
                    {
                        data: 'created_at',
                    },
                ]
            });
        }

        function gambarGrafik(a, b, boxDiv, tanggal) {
            var ctx = document.getElementById(boxDiv);
            ctx.height = 80;

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: a,
                    datasets: [{
                        label: 'TOTAL',
                        data: b,
                        backgroundColor: '#1A237E',
                    }]
                },
                options: {
                    plugins: {
                        datalabels: {
                            display: true,
                            color: '#000000',
                            anchor: 'end',
                            align: 'top',
                        },
                    },
                    legend: {
                        display: true
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            stacked: true,
                            ticks: {
                                autoSkip: false,
                            },
                            afterDataLimits(scale) {
                                scale.max += 5;
                            }
                        }],
                        yAxes: [{
                            display: true,
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                                fontSize: 11,
                            },

                        }],
                    },
                }
            });

            // ctx.onclick = function(evt) {
            //     var activePoints = myChart.getElementsAtEvent(evt);
            //     var chartData = activePoints[0]['_chart'].config.data;
            //     var idx = activePoints[0]['_index'];

            //     if (tipe !== 'outlet') {
            //         var label = chartData.labels[idx];
            //     } else {
            //         var label = 'outlet';
            //     }

            //     // var value = chartData.datasets[0].data[idx];

            //     openModalOOS(tipe, label, '#modalCHART', tanggal);
            // };
        }
    </script>
@endsection
