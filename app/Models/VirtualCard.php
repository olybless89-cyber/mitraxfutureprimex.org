<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualCard extends Model
{
    protected $fillable = ['user_id', 'status', 'last4', 'expiry', 'admin_note', 'approved_at'];

    protected $dates = ['approved_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
