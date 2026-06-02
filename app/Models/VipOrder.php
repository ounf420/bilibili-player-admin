<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipOrder extends Model
{
    protected $fillable = ['order_no','user_id','plan_id','amount','payment_method','status','paid_at','start_at','expire_at'];
    
    protected $casts = ['paid_at' => 'datetime','start_at' => 'datetime','expire_at' => 'datetime'];
    
    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(VipPlan::class); }
}
