<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Instance extends BaseModel
{
    protected $fillable = [
        'user_id', 'container_id', 'rancher_container_id', 'cloudware_id', 'memory'
    ];

    public function cloudware()
    {
        return $this->belongsTo('App\Models\Cloudware');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function submission()
    {
        return $this->hasOne('App\Models\Submission');
    }
}
