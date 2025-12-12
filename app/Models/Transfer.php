<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $guarded =[];

    public function fromAccount(){
        return $this->belongsTo(Account::class,'from_account');
    }

    public function toAccount(){
        return $this->belongsTo(Account::class,'to_account');
    }
}
