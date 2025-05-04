<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'freelancer_id',
        'amount',
        'delivery_days',
        'cover_letter',
        'status',
        'credits_used'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'delivery_days' => 'integer',
        'credits_used' => 'integer'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}