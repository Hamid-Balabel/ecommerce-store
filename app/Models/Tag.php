<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use translatable;

    protected $with=['translations'];

    protected $translatedAttributes= ['name'];

    protected $fillable = ['slug'];

    protected $hidden=['translations'];


    protected $casts=[
        'is_active'=>'boolean',
    ];



}
