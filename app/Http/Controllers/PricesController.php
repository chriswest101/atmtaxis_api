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
        $validator = Validator::make(
            [
                'distance' => $request->distance
            ],
            [
                'distance' => 'required|numeric|min:0',
            ]);

        if ($validator->fails())
        {
          return response()->json(['errors' => $validator->errors()], 400);
        }
        else
        {
            $minimumDistance = Config::where('name', '=', 'MINIMUM_DISTANCE')->first();
            $lowerMultiplier = PriceMultiplier::where('name', '=', 'LOWER')->first();
            $upperMultiplier = PriceMultiplier::where('name', '=', 'UPPER')->first();
            $rate = Rate::where('name', '=', 'DAY')->first();

            $distance = (float)$request->distance < (float)$minimumDistance->value ? (float)$minimumDistance->value : (float)$request->distance;
            $price = preg_replace("/[^0-9,.]/", "", number_format($distance * $rate->value, 2, '.', ''));
            $lowerPrice = number_format((float)round(($price*$lowerMultiplier->value)), 2, '.', '');
            $upperPrice = number_format((float)round(($price*$upperMultiplier->value)), 2, '.', '');

            return response()->json([
                'estimates' => [
                    'upper' => $upperPrice,
                    'lower' => $lowerPrice,
                    'price' => $price,
                ]
            ], 200);
        }
    }
}
