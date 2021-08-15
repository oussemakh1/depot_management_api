<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    /**
     * The attributes that are mass assigned
     * @var array 
     */
    protected $fillable = [
        "name",
        "size",
        "weight",
        "cost",
        "quantity",
        "type",
        "expiredat",
        "depot_id",
    ];
}