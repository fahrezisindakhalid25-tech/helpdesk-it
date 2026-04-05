<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\Master\LocationFactory::new();
    }

    protected $table = 'ms_locations';

    protected $fillable = ['name'];
}
