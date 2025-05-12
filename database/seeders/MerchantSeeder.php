<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('merchants')->insert([
            [
                'user_toko' => 'ORANG PINTAR TAU KUALITAS BUKAN TAU HARGA MURAH" BE SMART BUYER ... SUPPLIER BAJU BATIK PEKALONGAN TERMURAH. BERANI BERSAING DENGAN TOKO SEBELAH DARI HARGA YANG TERMURAH. PEMBELIAN KODIAN/ 20 PCS HARGA KHUSUS. MENERIMA PESANAN SERAGAM (KHUSUS BATIK PEKALONGAN)',
                'nama_toko' => 'BATIK MURAH JAYA PEKALONGAN',
                'link' => 'https://www.bukalapak.com/u/aisyah_isa546',
            ]
        ]);
    }
}