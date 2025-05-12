<?php

namespace App\Models;
use DB;
use Session;
class Scrapper
{
	public static function initiateToken($url)
	{
		// $url = 'https://www.bukalapak.com/p/rumah-tangga/furniture-interior/dekorasi-rumah/3g3803l-jual-hiasan-dinding-kaligrafi-allah-muhammad-ayat-kursi-biru-motif-tropical';
		$result = [];
		$result['data'] = null;
		$result['status'] = true;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			$result['data'] = 'Error: ' . curl_error($ch);
			$result['status'] = false;
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
		        if (strpos($jsonLdContent, 'localStorage.setItem') !== false) 
		        {
		        	$jsonData = $jsonLdContent;
		        }
		}
		$jsonData = str_replace("localStorage.setItem('bl_token', '", "", $jsonData);
		$jsonData = str_replace("');", "", $jsonData);
		$jsonData = json_decode($jsonData, true);
		if(!isset($jsonData['access_token']))
		{
			$result['data'] = 'Error: Access Token Not Found';
			$result['status'] = false;
		}else
		{
			$result['data'] = $jsonData['access_token'];
		}

		return $result;
	}

	public static function checkTokenOrGetData($token,$product_id,$productUrl,$action,$source,$offset)
	{
		$result = [];
		$result['status'] = true;
		$result['data'] = null;
		//hit api review
		$reviewUrl = 'https://api.bukalapak.com/product-reviews?limit=5&offset='.$offset.'&product_id='.$product_id.'&access_token='.$token.'';
		$ch = curl_init($reviewUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$responseReview = curl_exec($ch);
		if (curl_errno($ch)) 
		{
			$result['status'] = false;
			$result['data'] = 'Error: ' . curl_error($ch);
		}

		// Tutup koneksi cURL
		curl_close($ch);
		$dataReview = json_decode($responseReview, true);
		if($action == 'check_token')
		{
			if($dataReview['meta']['http_status'] != 200)
			{
				$token = static::initiateToken($productUrl);
				if(!$token['status'])
				{
					$result['status'] = false;
					$result['data'] = 'Error: ' . $token['data'];
				}else
				{
					if($source == 'web')
					{
						Session::put('bl_token',$token['data']);
					}else{
						$result['data'] = $token['data'];
					}
				}
			}
		}else
		{
			if($dataReview['meta']['http_status'] == 200)
			{
				$result['data'] = $dataReview;
			}
		}

		return $result;
	}
}