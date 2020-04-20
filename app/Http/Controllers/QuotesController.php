<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Quote;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class QuotesController extends Controller
{
    public function getAll()
    {
        return Auth::user()->quotes;
    }

    public function get(string $quoteId)
    {
        $validator = Validator::make(['quoteId' => $quoteId], ['quoteId' => 'required|numeric']);

        if ($validator->fails())
        {
          return response()->json(['errors' => $validator->errors()], 400);
        }

        return Booking::findOrFail($quoteId);
    }

    public function create(Request $request)
    {
        $quoteData = [
            'from_destination' => $request->from_destination,
            'from_latlong' => $request->from_latlong,
            'to_destination' => $request->to_destination,
            'to_latlong' => $request->to_latlong,
            'date' => $request->date,
            'time' => $request->time,
            'name' => $request->name,
            'email' => $request->email,
            'no_of_people' => $request->no_of_people,
            'distance' => $request->distance
        ];
        $validator = Validator::make(
            $quoteData,
            [
                'from_destination' => 'required|string',
                'from_latlong' => 'required|string',
                'to_destination' => 'required|string',
                'to_latlong' => 'required|string',
                'date' => 'required|date_format:Y-m-d',
                'time' => 'required',
                'name' => 'required|string',
                'email' => 'required|email',
                'no_of_people' => 'required|numeric|min:1|max:10',
                'distance' => 'required|numeric|min:0'
            ]);

        if ($validator->fails())
        {
          return response()->json(['errors' => $validator->errors()], 400);
        }
        else
        {
            $user = User::where('email', $request->email)->first();
            if (!$user)
            {
                $user = new User([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make(Str::random(40)),
                    'guest_account' => true,
                    'api_token' => Str::random(60)
                ]);
                $user->save();
                $user = User::where('email', $request->email)->first();
            }
            else
            {
                $user->email = $request->email;
                $user->save();
            }

            $quote = new Quote($quoteData);
            $user->quotes()->save($quote);

            return response()->json([
                'message' => 'Successfully created quote!'
            ], 201);
        }
    }
}
