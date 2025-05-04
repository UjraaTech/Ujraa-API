<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role',
        'assigned_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}