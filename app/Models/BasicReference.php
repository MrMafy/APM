<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicReference extends Model
{
    protected $table = 'basic_reference';
    protected $fillable = [
        'project_num',
        'projName',
        'projCustomer',
        'startDate',
        'endDate',
        'projGoal',
        'projCurator',
        'projManager',
        'payment'
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class,  'project_num', 'projNum');
    }

}
