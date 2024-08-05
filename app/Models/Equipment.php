<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'project_num',
        'nameTMC',
        'manufacture',
        'unit',
        'count',
        'priceUnit',
        'price',
        'equipment_file',
        'equipment_fileName'
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class,  'project_num', 'projNum');
    }
}
