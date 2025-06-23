<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Product([
            'kode_product' => $row['kode_product'],
            'nama_product' => $row['nama_produk'],
            'barcode' => $row['barcode'],
            'satuan' => $row['satuan'],
            'jenis' => $row['jenis'],
            'merek' => $row['merek'],
            'fk_id_toko' => $row['id_toko'],
            'stock_product' => 0, // default stock
            'harga_jual' => 0     // default harga_jual
        ]);
    }
}