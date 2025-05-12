<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\DataDivision;
use App\Models\Dictionary;
use DB;
class UlasanProcessedText extends Controller
{
    public function labelingDatLatih(Request $request)
    {
        try {
            $merchants = DB::table("merchants")->get();
            $data = [];

            if ($request->merchant_id || $request->product_id || $request->latih || $request->uji) {
               // dd('oke');
                if ($request->product_id == null) {
                    return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
                }
               // dd('oke');
                $latih = intval($request->latih);
                $uji = intval($request->uji);
                $totalData = $latih + $uji;
                $total = 100;
                if ($totalData > $total) {
                    return redirect()->back()->with('error', 'Jumlah data latih dan data uji tidak boleh lebih dari 100%');
                } else if ($totalData < $total) {
                    return redirect()->back()->with('error', 'Jumlah data latih dan data uji harus 100%');
                }
                //dd('oke');
                $product_id = $request->product_id;
                // $check = DB::table('data_divisions as dd')
                //     ->join('reviews as r', 'r.id', '=', 'dd.review_id')
                //     ->where('r.product_id', $product_id)
                //     ->get();
    
                //if (count($check) > 0) {
                    if ($request->reset != null) {
                         //dd('kenek');
                        DB::table('data_divisions')->truncate();
                        DB::table('reviews')->update([
                            'label' => null,
                            'bobot_label' => null,
                        ]);

                        $data = [];
                        $data["latih"] = $latih / 100; // 20%
                        $data["uji"] = $uji / 100; // 80%
                        $result = $this->bagiData($data); //script jalanin buat pembagian data

                        foreach ($result["latih"] as $value) {
                            DB::table("data_divisions")->insert([
                                "review_id" => $value,
                                "type" => "train",
                            ]);
                        }

                        foreach ($result["uji"] as $value1) {
                            DB::table("data_divisions")->insert([
                                "review_id" => $value1,
                                "type" => "test",
                            ]);
                        }

                        $dataReviews = Review::select("id", "stemming")->get();
                        $lexion = $this->labelingDataWithDictionary($dataReviews); //insert lexion

                        $dataLatih = DB::table("data_divisions")
                            ->join('reviews', 'reviews.id', '=', 'data_divisions.review_id')
                            ->where('product_id', $product_id)
                            ->where("type", "train")
                            ->get();
                        $train = [];
                        foreach ($dataLatih as $value) {
                            $train[] = $value->review_id;
                        }

                        $data = DB::table("reviews as rv")
                            ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                            ->whereIn("rv.id", $train)
                            ->select(
                                "rv.konten",
                                "rv.bobot_label",
                                "rv.label",
                                "dd.label_arr"
                            )
                            ->get();
                            // return response()->json($data);
                            return view(
                                "pages.ulasan-processed-text.data-latih",
                                compact("request", "data", "merchants")
                            );
                        //return redirect("ulasan_processed_text/labeling_data_latih")->with('success', 'Data berhasil direset');
                    } else {
                        $dataLatih = DB::table("data_divisions")
                            ->join('reviews', 'reviews.id', '=', 'data_divisions.review_id')
                            ->where('product_id', $product_id)
                            ->where("type", "train")
                            ->get();
                            // return response()->json($dataLatih);
                        $train = [];
                        foreach ($dataLatih as $value) {
                            $train[] = $value->review_id;
                        }
                        $data = DB::table("reviews as rv")
                            ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
                            ->whereIn("rv.id", $train)
                            ->select(
                                "rv.konten",
                                "rv.bobot_label",
                                "rv.label",
                                "dd.label_arr"
                            )
                            ->get();
                            // return response()->json($data);
                            return view(
                                "pages.ulasan-processed-text.data-latih",
                                compact("request", "data", "merchants")
                            );
                    }
               // }
            } else {
                $data = [];
                return view(
                    "pages.ulasan-processed-text.data-latih",
                    compact("request", "data", "merchants")
                );
            }
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            return $errorLine . " | " . $errorMessage;
        }
    }

    public function dataUji()
    {
        $uji = [];
        $dataUji = DB::table("data_divisions")
            ->where("type", "test")
            ->get();
        foreach ($dataUji as $key => $value) {
            $uji[$key] = $value->review_id;
        }
        $data = DB::table("reviews as rv")
            ->join("data_divisions as dd", "dd.review_id", "=", "rv.id")
            ->whereIn("rv.id", $uji)
            ->select("rv.konten", "rv.bobot_label", "rv.label", "dd.label_arr")
            ->get();
        return view("pages.ulasan-processed-text.data-uji", compact("data"));
    }

    public function bagiData($data)
    {
        $result = [];
        $dataSet = DB::table("reviews")->get();
        $total = count($dataSet);

        $result["latih"] = intval(round($total * $data["latih"]));
        $result["uji"] = intval(round($total * $data["uji"]));
        $result["total"] = $result["uji"] + $result["latih"];

        $result["data_latih"] = DB::table("reviews")
            ->inRandomOrder()
            ->limit($result["latih"])
            ->get();
        $arrLatih = [];
        foreach ($result["data_latih"] as $key => $value) {
            $arrLatih[$key] = $value->id;
        }

        $result["data_uji"] = DB::table("reviews")
            ->whereNotIn("id", $arrLatih)
            ->get();
        $arrUji = [];
        foreach ($result["data_uji"] as $key1 => $value1) {
            $arrUji[$key1] = $value1->id;
        }

        $arr = [];
        $arr["latih"] = $arrLatih;
        $arr["uji"] = $arrUji;
        return $arr;
    }

    public function labelingDataWithDictionary($data)
    {
        foreach ($data as $key => $value) {
            $textDataArr = json_decode($value->stemming, true);
            $bobot = 0;
            $arrDivisionLabel = [];
            $no = -1;
            foreach ($textDataArr as $key1 => $value1) {
                //positif & negatif
                if ($value1 != "") {
                    $dictionarySearch = Dictionary::where("word", $value1)
                        ->where("type", "positive")
                        ->first(); //kamus sentimen positif
                    if ($dictionarySearch) {
                        $no++;
                        $bobot += $dictionarySearch->bobot;
                        $arrDivisionLabel[$no] =
                            $value1 . ":" . $dictionarySearch->bobot;
                    } else {
                        $dictionarySearch = Dictionary::where("word", $value1)
                            ->where("type", "negative")
                            ->first(); //kamus sentimen negatif
                        if ($dictionarySearch) {
                            $no++;
                            $bobot += $dictionarySearch->bobot;
                            $arrDivisionLabel[$no] =
                                $value1 . ":" . $dictionarySearch->bobot;
                        }
                    }
                }
            }

            $resultBobot = "netral";
            if ($bobot > 0) {
                $resultBobot = "positif";
            } elseif ($bobot < 0) {
                $resultBobot = "negatif";
            }

            DB::table("reviews")
                ->where("id", $value->id)
                ->update([
                    "bobot_label" => $bobot,
                    "label" => $resultBobot,
                ]);

            $arrDivisionLabel = json_encode($arrDivisionLabel);
            DB::table("data_divisions")
                ->where("review_id", $value->id)
                ->update([
                    "label_arr" => $arrDivisionLabel,
                ]);
        }
    }
}
