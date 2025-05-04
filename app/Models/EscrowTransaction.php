<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EscrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'client_id',
        'freelancer_id',
        'amount',
        'platform_fee',
        'is_first_collaboration',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'is_first_collaboration' => 'boolean'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}