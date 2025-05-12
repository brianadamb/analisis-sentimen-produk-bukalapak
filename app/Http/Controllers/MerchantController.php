<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantController extends Controller
{

    public function index()
    {
        $data = Merchant::all();
        return view('pages.merchant.list',compact('data'));
    }

    public function store(Request $request)
    {

        try {
            DB::table('merchants')->insert([
                'user_toko' => $request->user_toko,
                'nama_toko' => $request->nama_toko,
                'link' => $request->link,
                'created_at' => now(),
                'updated_at' => now()
            ]);
             //dd($request->all()); 
            return redirect()->back()->with('success','Berhasil menambahkan data');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::table('merchants')->where('id', $id)->update([
                'user_toko' => $request->user_toko,
                'nama_toko' => $request->nama_toko,
                'link' => $request->link,
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
            DB::table('merchants')->where('id', $id)->delete();
            return redirect()->back()->with('success','Berhasil menghapus data');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Gagal menghapus data: ' . $e->getMessage());
        }
    }
}