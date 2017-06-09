<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Cloudware extends BaseModel
{
    protected $fillable = [
        'name', 'description', 'image', 'logo'
    ];
}
