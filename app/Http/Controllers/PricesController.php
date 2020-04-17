<?php

namespace App\Http\Controllers;

use App\Config;
use App\PriceMultiplier;
use App\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricesController extends Controller
{
    public function getEstimate(Request $request)
    {
        $data = [
            'distance' => $request->distance
        ];
        $validator = Validator::make(
            $data,
            [
                'distance' => 'required|numeric',
            ]);

        if ($validator->fails())
        {
          return response()->json(['errors' => $validator->errors()], 400);
        }
        else
        {
            $minimumPrice = Config::where('name', '=', 'MINIMUM_PRICE')->first();
            $defaultPrice = Config::where('name', '=', 'DEFAULT_PRICE')->first();
            $distance = $request->distance;
            $rate = Rate::where('name', '=', 'DAY')->first();
            $lowerMultiplier = PriceMultiplier::where('name', '=', 'LOWER')->first();
            $upperMultiplier = PriceMultiplier::where('name', '=', 'UPPER')->first();

            $price = preg_replace("/[^0-9,.]/", "", number_format((float)$distance * $rate->value, 2, '.', ''));
            $price = $price < $minimumPrice->value ? $defaultPrice->value : $price;
            $lowerPrice = number_format((float)round(($price*$lowerMultiplier->value)), 2, '.', '');
            $upperPrice = number_format((float)round(($price*$upperMultiplier->value)), 2, '.', '');

            return response()->json([
                'estimates' => [
                    'upper' => $upperPrice,
                    'lower' => $lowerPrice,
                    'price' => $price,
                ]
            ], 201);
        }
    }
}
