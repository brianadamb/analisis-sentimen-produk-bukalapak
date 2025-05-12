<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use DB;

class ProductController extends Controller
{


    public function index()
    {        
        $data = Product::with('merchant')->get();
        $merchant = Merchant::all();
        return view('pages.product.list',compact('data','merchant'));
    }

    public function store(Request $request)
    {
        try {
            $merchant = Merchant::find($request->merchant_id);
            $idP = explode('/',$request->link);
            $idP = end($idP);
            $idP = explode('-',$idP);
            $idProduk = $idP[0];
            DB::table('products')->insert([
                'merchant_id' => $merchant->id,
                'nama_produk' => $request->nama_produk,
                'link' => $request->link,
                'id_produk'=>$idProduk,
                'created_at' => now(),
                'updated_at' => now()
            ]);

           return redirect()->back()->with('success','Berhasil menambahkan data');
        } catch (\Exception $e) {
           // dd($e->getMessage());
            return redirect()->back()->with('error','Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $idP = explode('/',$request->link);
            $idP = end($idP);
            $idP = explode('-',$idP);
            $idProduk = $idP[0];
            $merchant = Merchant::find($request->merchant_id);
            DB::table('products')->where('id', $id)->update([
                'merchant_id' => $merchant->id,
                'nama_produk' => $request->nama_produk,
                'link' => $request->link,
                'id_produk'=>$idProduk,
                'created_at' => now(),
                'updated_at' => now()
            ]);

             return redirect()->back()->with('success','Berhasil mengubah data');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Gagal mengubah data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {       
       try {
            DB::table('products')->where('id', $id)->delete();
            return redirect()->back()->with('success','Berhasil menghapus data');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Gagal menghapus data: ' . $e->getMessage());
        }
    }
}