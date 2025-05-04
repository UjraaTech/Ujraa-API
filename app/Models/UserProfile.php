<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'website',
        'company_name',
        'company_size',
        'hourly_rate',
        'avatar_url'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}