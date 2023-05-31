<?php

namespace App\Http\Controllers;

use App\Exports\ScannerExport;
use App\Facade\Weblog;
use App\Models\Anggota;
use App\Models\AnggotaScanner;
use App\Models\Scanner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Laratrust\LaratrustFacade as Laratrust;
use Proengsoft\JsValidation\Facades\JsValidatorFacade;
use Symfony\Component\Uid\Ulid;

class ScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:scanner-read')->only('index');
        $this->middleware('permission:scanner-create')->only(['create', 'store']);
        $this->middleware('permission:scanner-update')->only(['edit', 'update']);
        $this->middleware('permission:scanner-delete')->only('delete');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.scanner.index');
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;
        $data = Scanner::query()
            ->when($cari, function ($e, $cari) {
                $e->where('label', 'like', '%' . $cari . '%');
            })
            ->where('status', $request->status)
            ->orderBy('id');

        if ($request->filled('export')) {
            Weblog::set('Export data scanner');
            return Excel::download(new ScannerExport($data->get()), 'SCANNER.xlsx');
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($e) {
                return Carbon::parse($e->created_at)->timezone(zona_waktu())->isoFormat('DD MMM YYYY HH:mm');
            })
            ->addColumn('link', function ($e) {
                return '<a href="' . route('scanner.frontend', ['slug' => $e->slug]) . '" class="btn btn-sm btn-primary" target="_blank">tampilkan</a>';
            })
            ->addColumn('aksi', function ($e) {
                $btnEdit = Laratrust::isAbleTo('scanner-update') ? '<a href="' . route('scanner.edit', ['scanner' => $e->id]) . '" class="btn btn-xs "><i class="bx bx-edit"></i></a>' : '';
                $btnDelete = Laratrust::isAbleTo('scanner-delete') ?  '<a href="' . route('scanner.destroy', ['scanner' => $e->id]) . '" data-title="' . $e->label . '" class="btn btn-xs text-danger btn-hapus"><i class="bx bx-trash"></i></a>' : '';
                $btnReload = Laratrust::isAbleTo('scanner-update') ? '<a href="' . route('scanner.destroy', ['scanner' => $e->id]) . '" data-title="' . $e->label . '" data-status="' . $e->status . '" class="btn btn-outline-secondary btn-xs btn-hapus"><i class="bx bx-refresh"></i></i></a>' : '';

                if ($e->status == true) {
                    return $btnEdit . ' ' . $btnDelete;
                } else {
                    return $btnReload;
                }
            })
            ->rawColumns(['aksi', 'link'])
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $validator = JsValidatorFacade::make([
            'label' => 'required',
            'keterangan' => 'nullable'
        ]);

        return view('backend.scanner.create', compact('validator'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $uuid = Ulid::generate();

        $request->merge([
            'slug' => $uuid,
        ]);

        $validasi = $request->validate([
            'label' => 'required',
            'keterangan' => 'nullable',
            'slug' => 'required',
        ]);

        DB::beginTransaction();
        try {
            Scanner::create($validasi);
            DB::commit();

            Weblog::set('Menambahkan Scanner: ' . $request->label);
            return redirect(route('scanner.index'))->with([
                'pesan' => '<div class="alert alert-success">Data berhasil ditambahkan</div>',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info($th->getMessage());

            return redirect()->back()->with([
                'pesan' => '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Scanner  $scanner
     * @return \Illuminate\Http\Response
     */
    public function show(Scanner $scanner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Scanner  $scanner
     * @return \Illuminate\Http\Response
     */
    public function edit(Scanner $scanner)
    {
        $validator = JsValidatorFacade::make([
            'label' => 'required',
            'keterangan' => 'nullable',
            'slug' => 'required',
        ]);

        return view('backend.scanner.edit', compact('validator', 'scanner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Scanner  $scanner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Scanner $scanner)
    {
        $validasi = $request->validate([
            'label' => 'required',
            'keterangan' => 'nullable',
            'slug' => 'required',
        ]);

        DB::beginTransaction();
        try {
            Scanner::find($scanner->id)->update($validasi);
            DB::commit();

            Weblog::set('Edit Scanner : ' . $request->label);
            return redirect(route('scanner.index'))->with([
                'pesan' => '<div class="alert alert-success">Data berhasil diperbarui</div>',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info($th->getMessage());

            return redirect()->back()->with([
                'pesan' => '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Scanner  $scanner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Scanner $scanner)
    {
        $status = $scanner->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                Scanner::find($scanner->id)->update(['status' => false]);
                Weblog::set('Menghapus scanner : ' . $scanner->label);
            } else {
                Scanner::find($scanner->id)->update(['status' => true]);
                Weblog::set('Mengaktifkan scanner : ' . $scanner->label);
            }

            DB::commit();

            return response()->json([
                'pesan' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Weblog::set($th->getMessage());
            return response()->json([
                'pesan' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    public function tampilan($slug)
    {
        $data = Scanner::where('slug', $slug)->where('status', true)->first();
        if($data === null){
            abort('404');
        }
        return view('backend.scanner.frontend', compact('data'));
    }

    public function checkin(Request $request)
    {

        $idGelang = $request->idGelang;
        $idScanner = $request->idScanner;

        $userid = Anggota::where('nomor_anggota', $idGelang)->first();
        $scanner = Scanner::where('slug', $idScanner)->first();
        if($userid === null){
            return $this->responError('Data napi tidak ditemukan');
        }

        if($scanner === null){
            return $this->responError('Data scanner tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            AnggotaScanner::create([
                'anggota_id' => $userid->id,
                'scanner_id' => $scanner->id,
            ]);
            DB::commit();

            return $this->responOk(data:$userid);

        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return $this->responError('Terjadi kesalahan');
        }

    }
}
