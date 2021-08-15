<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {

    /**
     * The attributes that are mass assigned
     * @var array
     */
    protected $fillable = [
        "product_id",
        "provider_id",
        "status",
        "quantity",
        "recivedat",
        "price",
        "discount",
        "total",
    ];
}