<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Sastrawi\Stemmer\StemmerFactory;
use App\Models\Dictionary;
use App\Models\Merchant;
use App\Models\Product;

class TextProcController extends Controller
{
    public function getData(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $product = Product::where('merchant_id', $merchant_id)->get();
        return response()->json([
            'product' => $product
        ]);
    }

    public function cleaning(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }
            $product = Product::where('id', $request->product_id)->first();
            $reviews = Review::where('product_id', $product->id)->get();
            foreach($reviews as $review)
            {
                $review->clean = $this->clean($review->konten);
                $review->save();
            }
            return view('pages.pre-processing.cleaning', compact('request' ,'reviews', 'merchants'));
        } else {
            $reviews = [];
            return view('pages.pre-processing.cleaning', compact('request' ,'reviews', 'merchants'));
        }
    }

    private function clean($text)
    {
         // Menghapus semua tanda baca dengan menggantinya dengan spasi
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Mengganti banyak spasi dengan satu spasi
        $text = preg_replace('/\s+/', ' ', $text);
        
        $text = preg_replace('/\d/', '', $text);
        // Memotong spasi di awal dan akhir string
        $text = trim($text);
        
        return $text;
    }

    public function removeEmojis($text) {
        $regexPattern = '/[\x{1F600}-\x{1F64F}]+|[\x{1F300}-\x{1F5FF}]+|[\x{1F680}-\x{1F6FF}]+|[\x{1F700}-\x{1F77F}]+|[\x{1F780}-\x{1F7FF}]+|[\x{1F800}-\x{1F8FF}]+|[\x{1F900}-\x{1F9FF}]+|[\x{1FA00}-\x{1FA6F}]+|[\x{1FA70}-\x{1FAFF}]+|[\x{2600}-\x{26FF}]+|[\x{2700}-\x{27BF}]+/u';
        
        return preg_replace($regexPattern, '', $text);
    }

    public function caseFolding(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }
            $product = Product::where('id', $request->product_id)->first();
            $reviews = Review::where('product_id', $product->id)->get();
            foreach ($reviews as $review) {
                $review->casefolding = $this->casefold($review->clean);
                $review->save();
            }
            return view('pages.pre-processing.casefolding', compact('request' ,'reviews', 'merchants'));
        } else {
            $reviews = [];
            return view('pages.pre-processing.casefolding', compact('request' ,'reviews', 'merchants'));
        }
    }

    public function casefold($text)
    {
        //mengubah semua huruf menjadi lowercase
        $text = strtolower($text);

        return $text;
    }

    public function normalization(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }
            $product = Product::where('id', $request->product_id)->first();
            $reviews = Review::where('product_id', $product->id)->get();
            foreach ($reviews as $review) {
                $review->normalization = $this->normalize($review->casefolding);
                $review->save();
            }
            return view('pages.pre-processing.normalization', compact('request' ,'reviews', 'merchants'));
        } else {
            $reviews = [];
            return view('pages.pre-processing.normalization', compact('request' ,'reviews', 'merchants'));
        }
    }

    private function normalize($text)
    {
        // penyempurnaan ke bahasa indonesia
        $wordcr = [
            ' brg ' => ' barang ',
            ' skrng ' => ' sekarang ',
            ' mantaappp ' => ' mantap ',
            ' cz ' => ' soalnya ',
            ' motiv ' => ' motif ',
            ' jg ' => ' juga ',
            ' tp ' => ' tapi ',
            ' sdh ' => ' sudah ',
            ' lg ' => ' lagi ',
            ' trus ' => ' terus ',
            ' jd ' => ' jadi ',
            ' yg ' => ' yang ',
            ' dtg ' => ' datang ',
            ' maudel ' => ' model ',
            ' engga ' => ' tidak ',
            ' cpt ' => ' cepat ',
            ' tks ' => ' terima kasih ',
            ' mnta ' => ' minta ',
            ' bhn ' => ' bahan ',
            ' dn ' => ' dan ',
            ' thx ' => ' terima kasih ',
            ' ok ' => ' ',
            ' oke ' => ' ',
            ' oke' => ' ',
            'oke' => ' ',
            ' ya ' => ' ',
            ' gk ' => ' tidak ',
            ' gak ' => ' tidak ',
            ' ga ' => ' tidak ',
            ' knp ' => ' kenapa ',
            ' kmrn ' => ' kemarin ',
            ' lbh ' => ' lebih ',
            ' sm ' => ' sama ',
            ' dr ' => ' dari ',
            ' dgn ' => ' dengan ',
            ' hrg ' => ' harga ',
            ' bgs ' => ' bagus ',
            ' uk ' => ' ukuran ',
            ' krn ' => ' karena ',
            ' kt ' => ' kita ',
            ' trs ' => ' terus ',
            ' blm ' => ' belum ',
            ' btw ' => ' by the way ',
            ' pdhl ' => ' padahal ',
            ' tpi ' => ' tapi ',
            ' plg ' => ' paling ',
            ' ptg ' => ' penting ',
            ' sy ' => ' saya ',
            ' gw ' => ' saya ',
            ' lo ' => ' kamu ',
            ' bgt ' => ' banget ',
            ' jln ' => ' jalan ',
            ' sblm ' => ' sebelum ',
            ' utk ' => ' untuk ',
            ' blh ' => ' boleh ',
            ' udh ' => ' sudah ',
            ' nih ' => ' ini ',
            ' aja ' => ' saja ',
            ' klo ' => ' kalau ',
            ' smg ' => ' semoga ',
            ' masi ' => ' masih ',
            ' msk ' => ' masuk ',
            ' brp ' => ' berapa ',
            ' bnyk ' => ' banyak ',
            ' cmn ' => ' cuma ',
            ' krg ' => ' kurang ',
            ' bbrp ' => ' beberapa ',
            ' aplg ' => ' apalagi ',
            ' tdk ' => ' tidak ',
            ' bs ' => ' bisa ',
            ' kmn ' => ' kemana ',
            ' km ' => ' kamu ',
            ' dtng ' => ' datang ',
            ' mending ' => ' lebih baik ',
            ' spt ' => ' seperti ',
            ' slh ' => ' salah ',
            ' smua ' => ' semua ',
            ' msh ' => ' masih ',
            ' bny ' => ' banyak ',
            ' gini ' => ' begini ',
            ' aj ' => ' saja ',
            ' bgtu ' => ' begitu ',
            ' udah ' => ' sudah ',
            ' w ' => ' saya ',
            ' gpp ' => ' tidak apa-apa ',
            ' nggak ' => ' tidak ',
            ' sumpah ' => ' sungguh ',
            ' gue ' => ' saya ',
            ' cm ' => ' cuma ',
            ' drpd ' => ' daripada ',
            ' mo ' => ' mau ',
            ' diem ' => ' diam ',
            ' bsr ' => ' besar ',
            ' brani ' => ' berani ',
            ' blanja ' => ' belanja ',
            ' bln ' => ' bulan ',
            ' x ' => ' kali ',
            ' jgn ' => ' jangan ',
            ' ni ' => ' ini ',
            ' kren ' => ' keren ',
            ' smp ' => ' sampai ',
            ' td ' => ' tadi ',
            ' pd ' => ' pada ',
            ' dri ' => ' dari ',
            ' smgt ' => ' semangat ',
            ' smw ' => ' semua ',
            ' tuh ' => ' itu ',
            ' kasi ' => ' kasih ',
            ' pas ' => ' tepat ',
            ' ny ' => ' nya ',
            ' gtu ' => ' gitu ',
            ' krna ' => ' karena ',
            ' dl ' => ' dulu ',
            ' bsa ' => ' bisa ',
            ' knpa ' => ' kenapa ',
            ' pk ' => ' pakai ',
            ' syg ' => ' sayang ',
            ' trm ' => ' terima ',
            ' mksh ' => ' makasih ',
            ' bngt ' => ' banget ',
            ' bnr ' => ' benar ',
            ' maen ' => ' main ',
            ' dpt ' => ' dapat ',
            ' nyoba ' => ' mencoba ',
            ' mnrt ' => ' menurut ',
            ' mlm ' => ' malam ',
            ' mnm ' => ' minum ',
            ' bsok ' => ' besok ',
            ' prnh ' => ' pernah ',
            ' kmi ' => ' kami ',
            ' nmr ' => ' nomor ',
            ' tlg ' => ' tolong ',
            ' skr ' => ' sekarang ',
            ' thn ' => ' tahun ',
            ' jl ' => ' jalan ',
            ' hri ' => ' hari ',
            ' jmlh ' => ' jumlah ',
            ' mn ' => ' mana ',
            ' dsb ' => ' dan sebagainya ',
            ' dll ' => ' dan lain-lain ',
            ' pls ' => ' please ',
            ' dpn ' => ' depan ',
            ' mksd ' => ' maksud ',
            ' dlm ' => ' dalam ',
            ' tmn ' => ' teman ',
            ' jgn ' => ' jangan ',
            ' kpn ' => ' kapan ',
            ' :( ' => ' ',
            ' g ' => ' tidak ',
            ' dateng ' => ' datang ',
            ' doang ' => ' saja ',
            ' zipp ' => ' mantap ',
            ' moga ' => ' semoga ',
            ' siip ' => ' mantap ',
            ' jg ' => ' juga '
        ];


        // $text = strtr($text, $wordcr);

        // // perbaiki huruf yang berlebihan
        // $text = preg_replace('/(.)\1{2,}/', '$1$1', $text);
        foreach ($wordcr as $key => $value) 
        {
            $text = str_replace($key, $value, $text);
        }
        $text = strtolower($text);
        return $text;
    }

    public function tokenizing(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }
            $product = Product::where('id', $request->product_id)->first();
            $reviews = Review::where('product_id', $product->id)->get();
            foreach ($reviews as $review) {
                $review->tokenizing = json_encode($this->tokenize($review->normalization));
                $review->save();
            }
            return view('pages.pre-processing.tokenizing', compact('request' ,'reviews', 'merchants'));
        } else {
            $reviews = [];
            return view('pages.pre-processing.tokenizing', compact('request' ,'reviews', 'merchants'));
        }
    }

    private function tokenize($text)
    {
       //memecah jadi array
        $text = explode(' ', $text);
        $result = [];
        $no = -1;
        foreach ($text as $key => $value) 
        {
           if($value != null)
           {
             $no++;
             $result[$no] = $value;
           }
        }
        return $result;
    }

    public function stopwordRemoval(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }
            $product = Product::where('id', $request->product_id)->first();
            $reviews = Review::where('product_id', $product->id)->get();
            $dictionary = Dictionary::where('type','stopword')->select('word')->get();
            $dictionaryArr = [];
            foreach ($dictionary as $key => $value) 
            {
                $dictionaryArr[$key] = $value;
            }
            foreach ($reviews as $review) {
                $review->stopword = $this->removeStopwords($review->normalization,$dictionaryArr);
                $review->save();
            }
            return view('pages.pre-processing.stopword', compact('request' ,'reviews', 'merchants'));
        } else {
            $reviews = [];
            return view('pages.pre-processing.stopword', compact('request' ,'reviews', 'merchants'));
        }
    }

    private function removeStopwords($text,$stopWords)
    {
        $kataKata = explode(" ", $text);
    
        // Filter kata-kata yang bukan stop words
        $hasil = array_filter($kataKata, function($kata) use ($stopWords) {
            return !in_array(strtolower($kata), $stopWords);
        });
        
        // Gabungkan kata-kata menjadi teks
        return implode(" ", $hasil);
    }

    public function stemming(Request $request)
    {
        $merchants = Merchant::all();
        if($request->merchant_id || $request->product_id)
        {
            if($request->product_id == null){
                return redirect()->back()->with('error', 'Pilih Produk terlebih dahulu');
            }
            $product = Product::where('id', $request->product_id)->first();
            $reviews = Review::where('product_id', $product->id)->get();
            foreach ($reviews as $review) {
                $kontenArray = explode(' ', $review->stopword);
                $review->stemming = json_encode($this->stemKan($kontenArray));
                $review->save();
            }
            return view('pages.pre-processing.stemming', compact('request' ,'reviews', 'merchants'));
        } else {
            $reviews = [];
            return view('pages.pre-processing.stemming', compact('request' ,'reviews', 'merchants'));
        }
    }

    public function stemKan($textArray)
    {
       // dd($textArray);
        $stemmerFactory = new StemmerFactory();
        $stemmer = $stemmerFactory->createStemmer();

        $stemmedText = [];
        if($textArray != null)
        {
            foreach ($textArray as $t) {
                $stemmedText[] = $stemmer->stem($t);
            }
        }
       // dd($stemmedText);
        return $stemmedText;
    }
}
