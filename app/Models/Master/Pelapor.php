<?php

namespace App\Models\Master;

use Database\Factories\Master\PelaporFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelapor extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PelaporFactory::new();
    }

    protected $table = 'ms_pelapor';

    protected $fillable = ['NIK', 'nama', 'email', 'no_hp'];
}
