<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'language',
        'is_verified',
        'identity_verified',
        'phone_verified'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified' => 'boolean',
        'is_verified' => 'boolean',
        'identity_verified' => 'boolean',
        'password' => 'hashed',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function skills()
    {
        return $this->hasMany(UserSkill::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function credits()
    {
        return $this->hasOne(UserCredit::class);
    }

    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function clientJobs()
    {
        return $this->hasMany(Job::class, 'client_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'freelancer_id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }
}
