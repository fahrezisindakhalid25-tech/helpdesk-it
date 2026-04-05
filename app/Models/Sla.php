<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sla extends Model
{
    protected $table = 'ms_service_level_agreements';
    protected $fillable = ['category_id', 'type', 'timeunit'];
}
