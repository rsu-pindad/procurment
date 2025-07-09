<?php

namespace App\Imports;

use App\Models\Ajuan;
use App\Models\Admin\{KategoriPengajuan,StatusAjuan,Unit,Vendor};
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class AjuanImport extends StringValueBinder implements ToCollection, WithCustomValueBinder
{
    protected $statusColumnMapping = [
        'ajuan' => 'Ajuan Unit',
        'pengadaan vendor' => 'Pengadaan Vendor',
        'aanwijzing' => 'Aanwijzing',
        'presentasi/demo/mockup' => 'Presentasi / Demo / Mockou',
        'negoasiasi' => 'Negosiasi',
        'penilaian' => 'Penilaian',
        'penyusunan pks' => 'Penyusunan PKS',
        'pelaksanaan/delivery' => 'Pelaksanaan / Delivery',
        'so/uji fungsi' => 'SO / Uji Fungsi',
        'bast' => 'BAST',
        'retensi' => 'Retensi',
        'pembayaran' => 'Pembayaran',
    ];

    public function collection(Collection $rows)
    {
        \App\Models\Ajuan::withoutEvents(function () use ($rows) {
            // Ambil header dan buat mapping kolom (lowercase => index)
            $header = $rows->shift();
            $headerMap = [];
            foreach ($header as $idx => $colName) {
                $headerMap[strtolower(trim($colName))] = $idx;
            }

            foreach ($rows as $row) {
                if (empty(trim($row[0]))) continue;

                $produk = trim($row[0]);
                $unitName = trim($row[1]);
                $hps = intval(str_replace(['.', ' ', 'Rp'], '', $row[2]));
                $kategoriName = trim($row[3]);
                $jenisAjuan = trim($row[4]);
                $hpsNego = isset($row[20]) ? intval(str_replace(['.', ' ', 'Rp'], '', $row[20])) : 0;

                $unit = Unit::firstOrCreate(['nama_unit' => $unitName]);

                $vendorName = trim($row[19] ?? '');
                $vendor = null;
                if ($vendorName !== '') {
                    $vendor = Vendor::firstOrCreate(['nama_vendor' => $vendorName]);
                }

                $kategori = KategoriPengajuan::firstOrCreate(['nama_kategori' => $kategoriName]);

                $ajuan = Ajuan::create([
                    'units_id' => $unit->id,
                    'produk_ajuan' => $produk,
                    'hps' => $hps,
                    'jenis_ajuan' => $jenisAjuan,
                    'tanggal_ajuan' => now(),
                    'tanggal_update_terakhir' => now(),
                    'status_ajuans_id' => 1,
                    'users_id' => Auth::id(),
                    'vendor_id' => $vendor?->id,
                    'hps_nego' => $hpsNego,
                ]);

                $ajuan->kategori_pengajuans()->syncWithoutDetaching([$kategori->id]);

                $ajuan->statusHistories()->syncWithoutDetaching([
                    1 => [
                        'updated_by' => Auth::id(),
                        'realisasi' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);

                $latestStatusId = null;

                foreach ($this->statusColumnMapping as $csvKey => $statusName) {
                    if (!isset($headerMap[strtolower($csvKey)])) continue;

                    $csvIndex = $headerMap[strtolower($csvKey)];
                    $realisasiRaw = trim($row[$csvIndex] ?? '');
                    if ($realisasiRaw === '') continue;

                    $statusModel = StatusAjuan::firstOrCreate(
                        ['nama_status_ajuan' => $statusName],
                        [
                            'urutan_ajuan' => StatusAjuan::max('urutan_ajuan') + 1,
                        ]
                    );

                    $exists = $ajuan->statusHistories()
                        ->wherePivot('status_ajuan_id', $statusModel->id)
                        ->wherePivotNotNull('realisasi')
                        ->exists();

                    if (!$exists) {
                        $ajuan->statusHistories()->syncWithoutDetaching([
                            $statusModel->id => [
                                'updated_by' => Auth::id(),
                                'realisasi' => $this->parseDateOrNull($realisasiRaw),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        ]);
                    }

                    $latestStatusId = $statusModel->id;
                }

                if ($latestStatusId) {
                    $ajuan->update([
                        'status_ajuans_id' => $latestStatusId,
                        'tanggal_update_terakhir' => now(),
                    ]);
                }
            }
        });
    }

    private function parseDateOrNull($value)
    {
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
