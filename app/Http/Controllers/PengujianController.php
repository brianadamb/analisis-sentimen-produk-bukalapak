<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Dictionary;
class PengujianController extends Controller
{
    public function run()
    {
        // Data pelatihan
        $dataTraining = DB::table('reviews as rv')
        				->join('data_divisions as dd','dd.review_id','=','rv.id')
        				->where('type','train')
        				->select('stemming as text','label')
        				->get();
        $dataTraining = json_decode(json_encode($dataTraining),true);
        foreach ($dataTraining as $key => $value) 
        {
        	$dataTraining[$key]['text'] = implode(' ', json_decode($value['text'],true));
        }

        $dataUji = DB::table('reviews as rv')
        				->join('data_divisions as dd','dd.review_id','=','rv.id')
        				->where('type','test')
        				->where('rv.label','!=',null)
        				->select('stemming as text','label')
        				->get();
        $dataUji = json_decode(json_encode($dataUji),true);
        foreach ($dataUji as $key1 => $value1) 
        {
        	$dataUji[$key1]['text'] = implode(' ', json_decode($value1['text'],true));
        }
        //dd($dataUji);
        // Daftar stop words (sederhana)
        $stopWords = array("sangat", "dengan", "dan", "ini", "tidak");

        // Menghitung Frekuensi Kata per Kategori
        function trainMultinomialNB($dataTraining, $stopWords) {
            $vocab = [];
            $categoryCounts = [];
            $wordCounts = [];

            foreach ($dataTraining as $data) {
                $label = $data["label"];
                $words = explode(" ", $data["text"]);
                if (!isset($categoryCounts[$label])) {
                    $categoryCounts[$label] = 0;
                    $wordCounts[$label] = [];
                }
                $categoryCounts[$label]++;
                foreach ($words as $word) {
                    if (!isset($vocab[$word])) {
                        $vocab[$word] = 0;
                    }
                    $vocab[$word]++;
                    if (!isset($wordCounts[$label][$word])) {
                        $wordCounts[$label][$word] = 0;
                    }
                    $wordCounts[$label][$word]++;
                }
            }

            return [$vocab, $categoryCounts, $wordCounts];
        }

        // Menghitung Probabilitas Prior dan Likelihood
        function calculateProbabilities($vocab, $categoryCounts, $wordCounts) {
            $totalDocs = array_sum($categoryCounts);
            $prior = [];
            $likelihood = [];
            $vocabSize = count($vocab);

            foreach ($categoryCounts as $label => $count) {
                $prior[$label] = $count / $totalDocs;
                $totalWords = array_sum($wordCounts[$label]);
                $likelihood[$label] = [];
                foreach ($vocab as $word => $count) {
                    $wordCount = isset($wordCounts[$label][$word]) ? $wordCounts[$label][$word] : 0;
                    $likelihood[$label][$word] = ($wordCount + 1) / ($totalWords + $vocabSize);
                }
            }

            return [$prior, $likelihood];
        }

        // Membuat Prediksi
        function predict($text, $prior, $likelihood, $vocab, $stopWords) {
            $words = explode(" ", $text);
            $scores = [];

            foreach ($prior as $label => $prob) {
                $scores[$label] = log($prob);
                foreach ($words as $word) {
                    if (isset($likelihood[$label][$word])) {
                        $scores[$label] += log($likelihood[$label][$word]);
                    } else {
                        $scores[$label] += log(1 / (array_sum($vocab) + count($vocab)));
                    }
                }
            }

            arsort($scores);
            return key($scores);
        }

        // Menghitung Confusion Matrix
        function calculateConfusionMatrix($dataUji, $prior, $likelihood, $vocab, $stopWords) {
            $labels = ["positif", "negatif", "netral"];
            $confusionMatrix = [];
            foreach ($labels as $label) {
                $confusionMatrix[$label] = array_fill_keys($labels, 0);
            }

            foreach ($dataUji as $data) {
                $actual = $data["label"];
                $predicted = predict($data["text"], $prior, $likelihood, $vocab, $stopWords);
                $confusionMatrix[$actual][$predicted]++;
            }

            return $confusionMatrix;
        }

        // Menghitung metrik evaluasi (akurasi, presisi, recall, F1-score)
        function calculateMetrics($confusionMatrix) {
            $labels = array_keys($confusionMatrix);
            $metrics = [];
            $totalTP = 0;
            $totalFP = 0;
            $totalFN = 0;
            $totalSamples = 0;

            foreach ($labels as $label) {
                $TP= $confusionMatrix[$label][$label];
                $FP = 0;
                $FN = 0;
                $TN = 0;

                foreach ($labels as $otherLabel) {
                    if ($otherLabel != $label) {
                        $FP += $confusionMatrix[$otherLabel][$label];
                        $FN += $confusionMatrix[$label][$otherLabel];
                        $TN += $confusionMatrix[$otherLabel][$otherLabel];
                    }
                }

                $totalTP += $TP;
                $totalFP += $FP;
                $totalFN += $FN;
                $totalSamples += array_sum($confusionMatrix[$label]);

                if($TP > 0)
		        {
		            $precision = $TP / ($TP + $FP);
		            $recall = $TP / ($TP + $FN);
		            $f1_score = 2 * (($precision * $recall) / ($precision + $recall));
		        }else
		        {
		            $precision = 1 / ($TP + 1);
		            $recall = $TP / ($TP + $FN);
		            $f1_score = 2 * (($precision * $recall) / ($precision + $recall));
		        }

                $metrics[$label] = [
                    "precision" => $precision,
                    "recall" => $recall,
                    "f1_score" => $f1_score
                ];
            }

            $accuracy = $totalTP / $totalSamples;

            return [$metrics, $accuracy];
        }

        // Melatih model
        list($vocab, $categoryCounts, $wordCounts) = trainMultinomialNB($dataTraining, $stopWords);
        list($prior, $likelihood) = calculateProbabilities($vocab, $categoryCounts, $wordCounts);

        // Menghitung confusion matrix
        $confusionMatrix = calculateConfusionMatrix($dataUji, $prior, $likelihood, $vocab, $stopWords);

        // Menghitung metrik
        list($metrics, $accuracy) = calculateMetrics($confusionMatrix);

	    $uji = [];
        $dataUji = DB::table('data_divisions')->where('type','test')->get();
        foreach ($dataUji as $key => $value) 
        {
            $uji[$key] = $value->review_id;
        }
        $positif = DB::table('reviews as rv')
                    ->join('data_divisions as dd','dd.review_id','=','rv.id')
                    ->whereIn('rv.id',$uji)
                    ->where('label','positif')
                    ->count();
        $negatif = DB::table('reviews as rv')
                    ->join('data_divisions as dd','dd.review_id','=','rv.id')
                    ->whereIn('rv.id',$uji)
                    ->where('label','negatif')
                    ->count();
        $all = DB::table('reviews as rv')
                    ->join('data_divisions as dd','dd.review_id','=','rv.id')
                    ->whereIn('rv.id',$uji)
                    ->select('stemming')
                    ->get();
        $arrAll = [];
        foreach ($all as $key => $value) 
        {
            $stm = json_decode($value->stemming,true);
            foreach ($stm as $i => $itm) 
            {
                if($itm != "")
                {
                    array_push($arrAll, $itm);
                }
            }
        }
        $arrAll = array_unique($arrAll);
        $oke = -1;
        $arrAllOke = [];
        foreach ($arrAll as $key => $value) 
        {
            $oke++;
            $arrAllOke[$oke] = $value;
        }
        $arrAllOke = json_encode($arrAllOke);
        $dataUji = DB::table('reviews as rv')
                    ->join('data_divisions as dd','dd.review_id','=','rv.id')
                    ->whereIn('rv.id',$uji)
                    ->select('rv.konten','rv.bobot_label','rv.label','dd.label_arr')
                    ->get();
	    return view('pages.ulasan-processed-text.hasil-prediksi',
                compact('confusionMatrix','metrics','positif','negatif','arrAllOke','dataUji'));	
    }
}
