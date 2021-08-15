<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depot extends Model 
{
    /**
     * The attributes that are mass assignable
     * @var array
     */
    protected $fillable = [
        "location",
        "size",
        "capacity",
        "type",
        "isRented",
        "rent",
    ];
}