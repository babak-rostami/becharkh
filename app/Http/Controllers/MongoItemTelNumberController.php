<?php

namespace App\Http\Controllers;

use App\Models\MongoItemTelNumber;
use Illuminate\Http\Request;

class MongoItemTelNumberController extends Controller
{
    public function saveNumber(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric'
        ]);

        try {
            $item_tel_number_last = MongoItemTelNumber::where('item_id', $request->item_id)->where('phone', $request->phone)->first();
            if (isset($item_tel_number_last)) {

                $count = $item_tel_number_last->count ?? 1;
                $item_tel_number_last->count = $count + 1;
                $item_tel_number_last->update();

                return response()->json([
                    'success' => true,
                    'message' => 'بزودی اضافه می‌شوید.'
                ]);
            }

            $item_tel_number = new MongoItemTelNumber();
            $item_tel_number->phone = $request->phone;
            $item_tel_number->item_id = $request->item_id;
            $item_tel_number->save();

            return response()->json([
                'success' => true,
                'message' => 'شماره ذخیره شد.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ذخیره شماره.'
            ], 500);
        }
    }

    public function indexAdmin()
    {
        $itel_numbers = MongoItemTelNumber::orderby('created_at', 'desc')->get();
        return view('item.admin.item-telegram-numbers', compact('itel_numbers'));
    }

    public function destroy(Request $request, $id)
    {
        $itel_numbers = MongoItemTelNumber::find($id);
        $itel_numbers->delete();
        return back()->with('success', 'با موفقیت حذف شد');
    }
}
