<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceMultiplier extends Model
{
    protected $fillable = [
        'name',
        'value'];
}
