<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Settings extends BaseModel
{
    protected $fillable = [
        'key', 'value'
    ];

}
