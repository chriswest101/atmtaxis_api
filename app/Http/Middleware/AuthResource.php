<?php

namespace App\Http\Middleware;

use App\Booking;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class AuthResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route('bookingId')) {
            $booking = Booking::find($request->route('bookingId'));
            if ($booking && $booking->user_id != Auth::user()->id) {
                return response()->json([
                    'message' => 'Unauthorised'
                ], 403);
            }
        }

        if ($request->route('quoteId')) {
            $quote = Booking::find($request->route('quoteId'));
            if ($quote && $quote->user_id != Auth::user()->id) {
                return response()->json([
                    'message' => 'Unauthorised'
                ], 403);
            }
        }

        return $next($request);
    }
}
