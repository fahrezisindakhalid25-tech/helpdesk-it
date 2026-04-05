<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Pelapor extends Model
{
    protected $table = 'ms_pelapor';

    protected $fillable = ['NIK', 'nama', 'email', 'no_hp'];
}
