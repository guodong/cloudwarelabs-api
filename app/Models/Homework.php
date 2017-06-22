<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Homework extends BaseModel
{
    protected $fillable = [
        'title', 'description', 'teacher_id'
    ];

    public function submissions()
    {
        return $this->hasMany('App\Models\Submission');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\User');
    }
}
