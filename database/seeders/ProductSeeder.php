<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Scrapper;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = 'https://www.bukalapak.com/p/fashion-pria/kemeja/gr7vqa-jual-kemeja-murah-kemeja-batik-termurah-kemeja-pria-lengan-pendek-termurah?from=list-product&pos=12&keyword=kemeja%20pria&funnel=omnisearch&product_owner=normal_seller&cf=1&ssa=0&sort_origin=weekly_sales_ratio%3Adesc&search_sort_default=false&promoted=0&search_query_id=5c2141b6fed37cd553141c480f6f94e3_614340672_1707581863536&acf=1&product_slot_type=organic&is_keyword_typo=false&keyword_expansion=undefined&search_result_size=3379';
        $namaProduk = 'kemeja murah - kemeja batik termurah - kemeja pria lengan pendek termurah';
        $idP = explode('/',$url);
        $idP = end($idP);
        $idP = explode('-',$idP);
        $idProduk = $idP[0];
        $produkId = DB::table('products')->insertGetId([
                        'merchant_id' => 1,
                        'nama_produk' => $namaProduk,
                        'link' => $url,
                        'id_produk'=>$idProduk,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

        //review
        $init = Scrapper::initiateToken($url);
        if($init['status'])
        {
           $offset = 0;
           $getData = Scrapper::checkTokenOrGetData($init['data'],$idProduk,$url,'get_data','migrations',$offset);
           if($getData['status'])
           {
                $metaTotal = $getData['data']['meta']['total'] / 5;
                for ($i=0; $i < round($metaTotal); $i++) 
                { 
                    $vData = Scrapper::checkTokenOrGetData($init['data'],$idProduk,$url,'get_data','migrations',$offset);
                    if($vData['status'])
                    {
                        $dataReview = $vData['data'];
                        foreach ($dataReview['data'] as $keyReview => $valueReview) 
                        {
                            $created_at = strtotime($valueReview['created_at']);
                            $created_at = date('Y-m-d H:i:s',$created_at);
                            $updated_at = strtotime($valueReview['updated_at']);
                            $updated_at = date('Y-m-d H:i:s',$updated_at);
                            DB::table('reviews')->insert([
                                'product_id'=> $produkId,
                                'title'=> $valueReview['review']['title'],
                                'konten'=> $valueReview['review']['content'],
                                'komen_name'=> $valueReview['sender']['name'],
                                'rate'=> $valueReview['review']['rate'],
                                'created_at'=> $created_at,
                                'updated_at'=> $updated_at
                            ]);
                        } 
                    }
                    $offset += 5;
                }
           }  
        }
    }
}