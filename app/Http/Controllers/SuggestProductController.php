<?php

namespace App\Http\Controllers;

use App\Models\SuggestProduct;
use Illuminate\Http\Request;

class SuggestProductController extends Controller
{
    public function suggestProduct(Request $request)
    {
        $new_product_suggest = new SuggestProduct();
        $new_product_suggest->body = $request->body;
        $new_product_suggest->save();
        return redirect()->back()->with('success', 'ممنون، پیشنهاد شما ثبت شد');
    }
}
