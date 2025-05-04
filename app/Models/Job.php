<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'budget',
        'status',
        'proposal_count',
        'deadline'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'datetime',
        'proposal_count' => 'integer'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function escrowTransactions()
    {
        return $this->hasMany(EscrowTransaction::class);
    }
}