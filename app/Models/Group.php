<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_groups', 'group_id', 'user_id');
    }
}
