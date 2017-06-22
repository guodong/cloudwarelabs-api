<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Submission extends BaseModel
{
    protected $fillable = [
        'user_id', 'homework_id', 'description'
    ];

    public function submissions()
    {
        return $this->hasMany('App\Models\Submission');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
