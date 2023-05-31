<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{
    public function index()
    {
        return view('backend.dashboard');
    }

    public function chart_sel(Request $request)
    {
        $tanggal = pecahTanggal($request->tanggal);

        $data = DB::table('anggota_scanners as a')
            ->select(
                'a.scanner_id',
                'b.label',
                DB::raw('COUNT(a.scanner_id) AS total'),
                'a.created_at',
            )
            ->join('scanners as b', 'b.id', '=', 'a.scanner_id')
            ->where(function ($e) use ($tanggal) {
                $e->whereDate('a.created_at', '>=', $tanggal[0])->whereDate('a.created_at', '<=', $tanggal[1]);
            })
            ->groupBy('a.scanner_id')
            ->get();

        return $this->responOk(data: $data);
    }

    public function chart_sel_napi(Request $request)
    {
        $tanggal = pecahTanggal($request->tanggal);

        $data = DB::table('anggota_scanners as a')
            ->select(
                'a.anggota_id',
                'c.nama',
                'c.nomor_anggota as id_gelang',
                'c.nomor_induk as id_napi',
                'c.jenis_kelamin',
                'a.scanner_id',
                'b.label',
                'a.created_at',
            )
            ->join('scanners as b', 'b.id', '=', 'a.scanner_id')
            ->join('anggotas as c', 'c.id', '=', 'a.anggota_id')
            ->where(function ($e) use ($tanggal) {
                $e->whereDate('a.created_at', '>=', $tanggal[0])->whereDate('a.created_at', '<=', $tanggal[1]);
            })
            ->orderByDesc('a.created_at');

        return DataTables::of($data)
            ->editColumn('created_at', function($e){
                return Carbon::parse($e->created_at)->timezone(zona_waktu())->isoFormat('DD MMM YYY HH:mm');
            })
            ->make(true);
    }
}
