<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'from_destination',
        'from_latlong',
        'to_destination',
        'to_latlong',
        'date',
        'time',
        'no_of_people',
        'distance',
        'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
