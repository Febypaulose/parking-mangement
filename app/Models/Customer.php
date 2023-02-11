<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Model
{
	 use SoftDeletes;
    use HasFactory;
    

   public function slot(){

    return $this->belongsTo(Slot::class);
}

}
