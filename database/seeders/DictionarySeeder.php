<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
class DictionarySeeder extends Seeder
{
	public function run(): void
    {
    	$data = [];

    	$stopWordBahasa = 'stopwordbahasa.txt';
    	$contentStopwrdBahasa = Storage::disk('local')->get($stopWordBahasa);
    	$contentStopwrdBahasa = explode("\r\n", $contentStopwrdBahasa);

    	$no = -1;
    	foreach ($contentStopwrdBahasa as $contentStopwrdBahasaKey => $contentStopwrdBahasaValue) 
    	{
    		if($contentStopwrdBahasaValue != null)
    		{
    			$no++;
	    		$data[$no]['word'] = $contentStopwrdBahasaValue;
	    		$data[$no]['type'] = 'stopword';
	    		$data[$no]['bobot'] = NULL;
    		}
    	}
    	//dd($data[1]);
    	$positif = 'positif.txt';
    	$contentPositif = Storage::disk('local')->get($positif);
    	$contentPositif = explode("\r\n", $contentPositif);

    	$bobotPositif = 'bobot_positif.txt';
    	$contentBobotPositif = Storage::disk('local')->get($bobotPositif);
    	$contentBobotPositif = explode("\r\n", $contentBobotPositif);

    	foreach ($contentPositif as $contentPositifKey => $contentPositifValue) 
    	{
    		$no++;
    		$data[$no]['word'] = $contentPositifValue;
    		$data[$no]['type'] = 'positive';
    		$data[$no]['bobot'] = $contentBobotPositif[$contentPositifKey];
    	}

    	$negatif = 'negatif.txt';
    	$contentNegatif = Storage::disk('local')->get($negatif);
    	$contentNegatif = explode("\r\n", $contentNegatif);

    	$bobotNegatif = 'bobot_negatif.txt';
    	$contentBobotNegatif = Storage::disk('local')->get($bobotNegatif);
    	$contentBobotNegatif = explode("\r\n", $contentBobotNegatif);

    	foreach ($contentNegatif as $contentNegatifKey => $contentNegatifValue) 
    	{
    		$no++;
    		$data[$no]['word'] = $contentNegatifValue;
    		$data[$no]['type'] = 'negative';
    		$data[$no]['bobot'] = $contentBobotNegatif[$contentNegatifKey];
    	}

    	DB::beginTransaction();
    	try {
    		DB::table('dictionaries')->insert($data);
    		DB::commit();
    	} catch (\Exception $e) {
    		DB::rollBack();
    		$gagal = 'Terjadi Kesalahan! | '.$e->getMessage() . ' | ' . $e->getLine();
    		dd($gagal);
    	}
    	// $data = [
    	// 	''
    	// ];
    }
}