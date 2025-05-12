<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Scrapper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Session;
use Excel;
use App\Exports\Dataset as DatasetExport;
class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $merchant = Merchant::all();
        $review = [];
        $product = [];
        if($request->product_id != null)
        {
            $checkReview = DB::table('reviews')->where('product_id',$request->product_id)->first();
            if(!$checkReview)
            {
                if($request->product_id == 'all')
                {
                    if($request->merchant_id == 'all')
                    {
                        $review = DB::table('reviews as rvw')
                                  ->join('products as pd','pd.id','=','rvw.product_id')
                                  ->join('merchants as mch','mch.id','=','pd.merchant_id')
                                  ->select('rvw.*')
                                  ->get();
                    }else
                    {
                        $product = DB::table('products')->where('merchant_id',$request->merchant_id)->get();
                        $review = DB::table('reviews as rvw')
                          ->join('products as pd','pd.id','=','rvw.product_id')
                          ->join('merchants as mch','mch.id','=','pd.merchant_id')
                          ->where('pd.merchant_id',$request->merchant_id)
                          ->select('rvw.*')
                          ->get();
                    }
                }else
                {
                    $data = DB::table('products')->where('id',$request->product_id)->first();
                    if($data)
                    {
                        $product = DB::table('products')->where('merchant_id',$request->merchant_id)->get();
                        $review = $this->syncOnline($data);
                    }
                }
            }else
            {   
                if($request->merchant_id == 'all')
                {
                    $review = DB::table('reviews as rvw')
                              ->join('products as pd','pd.id','=','rvw.product_id')
                              ->join('merchants as mch','mch.id','=','pd.merchant_id')
                              ->select('rvw.*')
                              ->get();
                }else
                {
                    if($request->product_id == 'all')
                    {
                        $product = DB::table('products')->where('merchant_id',$request->merchant_id)->get();
                        $review = DB::table('reviews as rvw')
                              ->join('products as pd','pd.id','=','rvw.product_id')
                              ->join('merchants as mch','mch.id','=','pd.merchant_id')
                              ->where('pd.merchant_id',$request->merchant_id)
                              ->select('rvw.*')
                              ->get();
                    }else
                    {
                        $product = DB::table('products')->where('merchant_id',$request->merchant_id)->get();
                        $review = DB::table('reviews as rvw')
                              ->join('products as pd','pd.id','=','rvw.product_id')
                              ->join('merchants as mch','mch.id','=','pd.merchant_id')
                              ->where('rvw.product_id',$request->product_id)
                              ->select('rvw.*')
                              ->get();
                    }
                   
                }
            } 
        }
        Session::forget('reviews');
        $dataIdReview = [];
        foreach ($review as $key => $value) {
           $dataIdReview[$key] = $value->id;
        }
        Session::put('reviews',$dataIdReview);
        return view('pages.review.list', compact('merchant','review','request','product'));
    }

    public function export()
    {
      return Excel::download(new DatasetExport, 'datasets.xlsx');
    }

    public function syncOnline($data)
    {
        $review = [];
        $url = $data->link;
        $idProduk = $data->id_produk;
        $produkId = $data->id;
        $init = Scrapper::initiateToken($url);
        if($init['status'])
        {
           $offset = 0;
           $getData = Scrapper::checkTokenOrGetData($init['data'],$idProduk,$url,'get_data','migrations',$offset);
           if($getData['status'])
           {
                DB::table('reviews')->where('product_id',$produkId)->delete();
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

                            //$checkAvail = DB::table('')
                            
                            DB::table('reviews')->insert([
                                'product_id'=> $produkId,
                                'title'=> $valueReview['review']['title'],
                                'konten'=> $valueReview['review']['title'].' '.$valueReview['review']['content'],
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
           $review = DB::table('reviews as rvw')
                      ->join('products as pd','pd.id','=','rvw.product_id')
                      ->join('merchants as mch','mch.id','=','pd.merchant_id')
                      ->where('rvw.product_id',$produkId)
                      ->select('rvw.*')
                      ->get();  
        }
        return $review;
    }

    public function product($id)
    {
        $products = Product::where('merchant_id', $id)->get();
        if(!$products->isEmpty())
        {
            return response()->json([
                'message' => 'success',
                'data' => $products
            ]);
        }else
        {
            return response()->json([
                'message' => 'error',
                'data' => $products
            ]);
        }
        //return response()->json($products);
    }
}