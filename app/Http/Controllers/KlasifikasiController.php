<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Dictionary;
use App\Models\Merchant;

class KlasifikasiController extends Controller
{
    private $documents = [];
    private $termFrequency = [];
    private $documentFrequency = [];
    private $tfIdf = [];

    public function data(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }

            $uji = [];
            $dataUji = DB::table("data_divisions")
                ->where("type", "test")
                ->get();
            foreach ($dataUji as $key => $value) {
                $uji[$key] = $value->review_id;
            }
            $dataUji = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->whereIn("rv.id", $uji)
                ->where("rv.product_id", $request->product_id)
                ->select("rv.konten", "rv.bobot_label", "rv.label", "dd.label_arr")
                ->get();
            // return response()->json($dataUji);
            return view("pages.klasifikasi.data", compact('request', 'merchants', 'dataUji'));
        } else {
            $dataUji = [];
            return view("pages.klasifikasi.data", compact('request', 'merchants', 'dataUji'));
        }
    }

    public function evaluasi(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }

            // Data pelatihan
            $dataTraining = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->where("type", "train")
                ->select("stemming as text", "label")
                ->get();
            $dataTraining = json_decode(json_encode($dataTraining), true);
            foreach ($dataTraining as $key => $value) {
                $dataTraining[$key]["text"] = implode(
                    " ",
                    json_decode($value["text"], true)
                );
            }

            $dataUji = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->where("type", "test")
                ->where("rv.label", "!=", null)
                ->select("stemming as text", "label")
                ->get();
            $dataUji = json_decode(json_encode($dataUji), true);
            foreach ($dataUji as $key1 => $value1) {
                $dataUji[$key1]["text"] = implode(
                    " ",
                    json_decode($value1["text"], true)
                );
            }
            //dd($dataUji);
            // Daftar stop words (sederhana)
            $stopWords = ["sangat", "dengan", "dan", "ini", "tidak"];

            // Menghitung Frekuensi Kata per Kategori
            function trainMultinomialNB($dataTraining, $stopWords)
            {
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

            //proses TF-IDF Data Training
            function calculateTfIdf() {
                $totalDocuments = count($dataTraining);

                foreach ($this->termFrequency as $docId => $terms) {
                    $this->tfIdf[$docId] = [];

                    foreach ($terms as $term => $count) {
                        $tf = $count / array_sum($terms);
                        $idf = log($totalDocuments / count($this->documentFrequency[$term]));
                        $this->tfIdf[$docId][$term] = $tf * $idf;
                    }
                }
            }

            // Menghitung Probabilitas Prior dan Likelihood
            function calculateProbabilities($vocab, $categoryCounts, $wordCounts)
            {
                $totalDocs = array_sum($categoryCounts);
                $prior = [];
                $likelihood = [];
                $vocabSize = count($vocab);

                foreach ($categoryCounts as $label => $count) {
                    $prior[$label] = $count / $totalDocs;
                    $totalWords = array_sum($wordCounts[$label]);
                    $likelihood[$label] = [];
                    foreach ($vocab as $word => $count) {
                        $wordCount = isset($wordCounts[$label][$word])
                            ? $wordCounts[$label][$word]
                            : 0;
                        $likelihood[$label][$word] =
                            ($wordCount + 1) / ($totalWords + $vocabSize);
                    }
                }

                return [$prior, $likelihood];
            }

            // Membuat Prediksi
            function predict($text, $prior, $likelihood, $vocab, $stopWords)
            {
                $words = explode(" ", $text);
                $scores = [];

                foreach ($prior as $label => $prob) {
                    $scores[$label] = log($prob);
                    foreach ($words as $word) {
                        if (isset($likelihood[$label][$word])) {
                            $scores[$label] += log($likelihood[$label][$word]);
                        } else {
                            $scores[$label] += log(
                                1 / (array_sum($vocab) + count($vocab))
                            );
                        }
                    }
                }

                arsort($scores);
                return key($scores);
            }

            // Menghitung Confusion Matrix
            function calculateConfusionMatrix(
                $dataUji,
                $prior,
                $likelihood,
                $vocab,
                $stopWords
            ) {
                $labels = ["positif", "negatif", "netral"];
                $confusionMatrix = [];
                foreach ($labels as $label) {
                    $confusionMatrix[$label] = array_fill_keys($labels, 0);
                }

                foreach ($dataUji as $data) {
                    $actual = $data["label"];
                    $predicted = predict(
                        $data["text"],
                        $prior,
                        $likelihood,
                        $vocab,
                        $stopWords
                    );
                    $confusionMatrix[$actual][$predicted]++;
                }

                return $confusionMatrix;
            }

            // Menghitung metrik evaluasi (akurasi, presisi, recall, F1-score)
            function calculateMetrics($confusionMatrix)
            {
                $labels = array_keys($confusionMatrix);
                $metrics = [];
                $totalTP = 0;
                $totalFP = 0;
                $totalFN = 0;
                $totalSamples = 0;

                foreach ($labels as $label) {
                    $TP = $confusionMatrix[$label][$label];
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

                    if ($TP > 0) {
                        $precision = $TP / ($TP + $FP);
                        $recall = $TP / ($TP + $FN);
                        $f1_score =
                            2 * (($precision * $recall) / ($precision + $recall));
                    } else {
                        $precision = 1 / ($TP + 1);
                        $recall = $TP / ($TP + $FN);
                        $f1_score =
                            2 * (($precision * $recall) / ($precision + $recall));
                    }

                    $metrics[$label] = [
                        "precision" => $precision,
                        "recall" => $recall,
                        "f1_score" => $f1_score,
                    ];
                }

                $accuracy = $totalTP / $totalSamples;

                return [$metrics, $accuracy];
            }

            // Melatih model
            list($vocab, $categoryCounts, $wordCounts) = trainMultinomialNB(
                $dataTraining,
                $stopWords
            );
            list($prior, $likelihood) = calculateProbabilities(
                $vocab,
                $categoryCounts,
                $wordCounts
            );

            // Menghitung confusion matrix
            $confusionMatrix = calculateConfusionMatrix(
                $dataUji,
                $prior,
                $likelihood,
                $vocab,
                $stopWords
            );

            // Menghitung metrik
            list($metrics, $accuracy) = calculateMetrics($confusionMatrix);
            // dd(round($accuracy, 2));
            return view(
                "pages.klasifikasi.evaluasi",
                compact("confusionMatrix", "metrics", "accuracy","merchants","request")
            );
        } else {
            $confusionMatrix = [];
            $metrics = [];
            $accuracy = 0;
            return view("pages.klasifikasi.evaluasi", compact("confusionMatrix", "metrics", "accuracy","merchants","request"));
        }
    }

    public function visualisasi(Request $request)
    {
        $merchants = Merchant::all();
        // $positif = $negatif = $netral = 0;
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }

            $uji = [];
            $dataUji = DB::table("data_divisions")
                ->where("type", "test")
                ->get();
            foreach ($dataUji as $key => $value) {
                $uji[$key] = $value->review_id;
            }
            $positif = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->whereIn("rv.id", $uji)
                ->where("label", "positif")
                ->count();
            $negatif = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->whereIn("rv.id", $uji)
                ->where("label", "negatif")
                ->count();
            $netral = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->whereIn("rv.id", $uji)
                ->where("label", "netral")
                ->count();

            $allPositif = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->whereIn("rv.id", $uji)
                ->where("label", "positif")
                ->select("stemming")
                ->get();
            $arrAllPositif = [];
            foreach ($allPositif as $key => $value) {
                $stm = json_decode($value->stemming, true);
                foreach ($stm as $i => $itm) {
                    if ($itm != "") {
                        $check = DB::table('dictionaries')->where('type','positive')->where('word',$itm)->first();
                        if($check)
                        {
                            array_push($arrAllPositif, $itm);
                        }
                    }
                }
            }
            $arrAllPositif = array_unique($arrAllPositif);
            $oke = -1;
            $arrAllOkePositif = [];
            foreach ($arrAllPositif as $key => $value) {
                $banyak = DB::table('reviews')
                    ->where('product_id', '=', $request->product_id)
                    ->where('konten', 'like', '%' . $value . '%')
                    ->count();
                if($banyak >= 40)
                {
                    $oke++;
                    $arrAllOkePositif[$oke] = $value;
                }
            }
            $arrAllOkePositif = json_encode($arrAllOkePositif);

            $positifJumlah = [];
            foreach ($arrAllPositif as $arrAllPositifKey => $arrAllPositifValue) {
                $banyak = DB::table('reviews')
                    ->where('product_id', '=', $request->product_id)
                    ->where('konten', 'like', '%' . $arrAllPositifValue . '%')
                    ->count();
                if($banyak >= 40)
                {
                    $positifJumlah[$arrAllPositifValue] = $banyak;
                }
            }

            $allNegatif = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->whereIn("rv.id", $uji)
                ->where("label", "negatif")
                ->select("stemming")
                ->get();
            $arrAllNegatif = [];
            foreach ($allNegatif as $key => $value) {
                $stm = json_decode($value->stemming, true);
                foreach ($stm as $i => $itm) {
                    if ($itm != "") {
                        $check = DB::table('dictionaries')->where('type','negative')->where('word',$itm)->first();
                        if($check)
                        {
                            array_push($arrAllNegatif, $itm);
                        }
                    }
                }
            }
            $arrAllNegatif = array_unique($arrAllNegatif);
            $oke = -1;
            $arrAllOkeNegatif = [];
            foreach ($arrAllNegatif as $key => $value) {
                $banyak = DB::table('reviews')
                    ->where('product_id', '=', $request->product_id)
                    ->where('konten', 'like', '%' . $value . '%')
                    ->count();
                if($banyak >= 40)
                {
                    $oke++;
                    $arrAllOkeNegatif[$oke] = $value;
                }
            }
            $arrAllOkeNegatif = json_encode($arrAllOkeNegatif);
            $negatifJumlah = [];
            foreach ($arrAllNegatif as $arrAllNegatifKey => $arrAllNegatifValue) {
                $banyak = DB::table('reviews')
                    ->where('product_id', '=', $request->product_id)
                    ->where('konten', 'like', '%' . $arrAllNegatifValue . '%')
                    ->count();
                if($banyak >= 40)
                {
                    $negatifJumlah[$arrAllNegatifValue] = $banyak;
                }
            }

            $allNetral = DB::table("reviews as rv")
                ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                ->where("rv.product_id", $request->product_id)
                ->whereIn("rv.id", $uji)
                ->where("label", "netral")
                ->select("stemming")
                ->get();
            $arrAllNetral = [];
            foreach ($allNetral as $key => $value) {
                $stm = json_decode($value->stemming, true);
                foreach ($stm as $i => $itm) {
                    if ($itm != "") {
                        $check = DB::table('dictionaries')->where('word',$itm)->first();
                        if($check)
                        {
                            array_push($arrAllNetral, $itm);
                        }
                    }
                }
            }
            $arrAllNetral = array_unique($arrAllNetral);
            $oke = -1;
            $arrAllOkeNetral = [];
            foreach ($arrAllNetral as $key => $value) {
                $banyak = DB::table('reviews')
                    ->where('product_id', '=', $request->product_id)
                    ->where('konten', 'like', '%' . $value . '%')
                    ->count();
                if($banyak >= 40)
                {
                    $oke++;
                    $arrAllOkeNetral[$oke] = $value;
                }
            }
            $arrAllOkeNetral = json_encode($arrAllOkeNetral);

            $netralJumlah = [];
            foreach ($arrAllNetral as $arrAllNetralKey => $arrAllNetralValue) {
                $banyak = DB::table('reviews')
                    ->where('product_id', '=', $request->product_id)
                    ->where('konten', 'like', '%' . $arrAllNetralValue . '%')
                    ->count();
                if($banyak >= 40)
                {
                    $netralJumlah[$arrAllNetralValue] = $banyak;
                }
            }

            return view(
                "pages.klasifikasi.visualisasi",
                compact(
                    "positif",
                    "negatif",
                    "netral",
                    "arrAllOkeNegatif",
                    "arrAllOkePositif",
                    "arrAllOkeNetral",
                    "positifJumlah",
                    "negatifJumlah",
                    "netralJumlah",
                    "merchants",
                    "request"
                )
            );
        } else {
            $positif = 0;
            $negatif = 0;
            $netral = 0;
            $arrAllOkeNegatif = json_encode([]);
            $arrAllOkePositif = json_encode([]);
            $arrAllOkeNetral =json_encode([]);
            $positifJumlah = [];
            $negatifJumlah = [];
            $netralJumlah = [];
            return view(
                "pages.klasifikasi.visualisasi",
                compact(
                    "positif",
                    "negatif",
                    "netral",
                    "arrAllOkeNegatif",
                    "arrAllOkePositif",
                    "arrAllOkeNetral",
                    "positifJumlah",
                    "negatifJumlah",
                    "netralJumlah",
                    "merchants",
                    "request"
                )
            );
        }
    }
}
