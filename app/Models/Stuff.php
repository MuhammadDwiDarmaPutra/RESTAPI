<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes; //optional digunakan hanya untuk table yang memakai fitur softdelete
    protected $fillable = ["name", "category"];

    public function stuffStock(){
        return $this->hasOne(StuffStock::class);
    }

    public function inboundStuff(){
        return $this->hasMany(InboundStuff::class);
    }

    public function lendings(){
        return $this->hasMany(Lending::class);
    }
}
