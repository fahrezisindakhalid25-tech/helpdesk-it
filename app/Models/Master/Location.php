<?php

namespace App\Models\Master;

use Database\Factories\Master\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return LocationFactory::new();
    }

    protected $table = 'ms_locations';

    protected $fillable = ['name'];
}
