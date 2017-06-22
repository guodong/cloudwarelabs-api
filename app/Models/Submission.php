<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Submission extends BaseModel
{
    protected $fillable = [
        'user_id', 'homework_id', 'description', 'instance_id'
    ];

    public function homework()
    {
        return $this->belongsTo('App\Models\Homework');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
