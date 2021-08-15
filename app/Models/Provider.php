<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model 
{
    /**
     * The attributes that are mass assigned
     * @var array 
     */
    protected $fillable = [
        "name",
        "email",
        "fax",
        "phone",
        "mat",
        "address",
        "country",
    ];
}