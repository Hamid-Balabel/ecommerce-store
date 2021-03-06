<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use translatable;

    protected $with=['translations'];

    protected $translatedAttributes= ['name'];

    protected $fillable = ['is_active','photo'];

//    protected $hidden=['translations'];


    protected $casts=[
        'is_active'=>'boolean',
    ];


    public function scopeActive($query){
        return $query->where('is_active',1);
    }


    public function getActive(){
        return $this-> is_active == 1 ? __('admin/general.active'):__('admin/general.not active');
    }

    public function getPhotoAttribute($val){
        return ($val !== null) ? asset('assets/images/brands/'. $val ): "";
    }

}
