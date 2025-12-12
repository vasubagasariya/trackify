<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Account;

class Transaction extends Model
{
    protected $guarded = [];
    
    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }
}
