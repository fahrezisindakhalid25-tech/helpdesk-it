<?php

namespace App\Models\Master;

use App\Models\Sla;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'ms_categories';

    protected $fillable = ['name'];

    public function sla()
    {
        return $this->belongsTo(Sla::class);
    }

    public function serviceLevelAgreements()
    {
        return $this->hasMany(Sla::class);
    }
}
