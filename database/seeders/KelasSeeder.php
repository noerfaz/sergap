<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ['1A', '1B', '1C', '2A', '2B', '2C', '3A', '3B', '3C'];

        $kategories = [];
        $kode = Kelas::orderby('id', 'desc')->first();

        if ($kode === NULL) {
            $i = 1;
            $kategories = $this->input_item($data, $i);
        } else {
            $last_kode = (int) substr($kode->kode, 2);
            $last_kode = $last_kode + 1;

            $kategories = $this->input_item($data, $last_kode);
        }

        Kelas::insert($kategories);
    }

    private function input_item($kategori_arr, $nomor)
    {
        foreach ($kategori_arr as $item) {
            $kategories[] = [
                'kode' => 'KK' . str_pad($nomor++, 4, '0', STR_PAD_LEFT),
                'kelas' => $item,
                'created_at' => Carbon::now(),
            ];
        }

        return $kategories;
    }
}
