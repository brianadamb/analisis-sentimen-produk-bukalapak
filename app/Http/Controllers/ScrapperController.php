<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
//use Carbon
class ScrapperController extends Controller
{
	//private $serverUrl;
	public function __construct()
    {
        // $this->middleware('auth');
    }

    public function testScrap()
    {
		$url = 'https://www.bukalapak.com/p/rumah-tangga/furniture-interior/dekorasi-rumah/3g3803l-jual-hiasan-dinding-kaligrafi-allah-muhammad-ayat-kursi-biru-motif-tropical';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error: ' . curl_error($ch);
		}
		curl_close($ch);
		$dom = new \DOMDocument;
		libxml_use_internal_errors(true);
		$dom->loadHTML($response);
		libxml_use_internal_errors(false);
		$scriptElements = $dom->getElementsByTagName('script');
		$jsonData = '';
		foreach ($scriptElements as $key => $scriptElement) 
		{
		        $jsonLdContent = $scriptElement->textContent;
		        if (strpos($jsonLdContent, 'localStorage.setItem') !== false) {
		        	$jsonData = $jsonLdContent;
		        }
		}
		$jsonData = str_replace("localStorage.setItem('bl_token', '", "", $jsonData);
		$jsonData = str_replace("');", "", $jsonData);
		$jsonData = json_decode($jsonData, true);

		//hit api review
		$reviewUrl = 'https://api.bukalapak.com/product-reviews?limit=5&offset=0&product_id=3g3803l&access_token='.$jsonData['access_token'].'';

		$ch = curl_init($reviewUrl);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$responseReview = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error: ' . curl_error($ch);
		}

		// Tutup koneksi cURL
		curl_close($ch);
		$dataReview = json_decode($responseReview, true);
		if($dataReview['meta']['http_status'] == 200)
		{
			foreach ($dataReview['data'] as $keyReview => $valueReview) 
			{
				$created_at = strtotime($valueReview['created_at']);
				$created_at = date('Y-m-d H:i:s',$created_at);
				$updated_at = strtotime($valueReview['updated_at']);
				$updated_at = date('Y-m-d H:i:s',$updated_at);
				DB::table('reviews')->insert([
					'product_id'=> 1,
					'title'=> $valueReview['review']['title'],
					'konten'=> $valueReview['review']['content'],
					'komen_name'=> $valueReview['sender']['name'],
					'rate'=> $valueReview['review']['rate'],
					'created_at'=> $created_at,
					'updated_at'=> $updated_at
				]);
			}
		}
		return 'done';
    }
}
