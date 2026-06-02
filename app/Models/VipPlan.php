<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipPlan extends Model
{
    protected $fillable = ['name','level','duration_days','price','sale_price','features','badge','is_active','sort_order'];
    
    protected $casts = ['features' => 'array', 'is_active' => 'boolean'];
}
