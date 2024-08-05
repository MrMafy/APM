<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'user_groups';

    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
