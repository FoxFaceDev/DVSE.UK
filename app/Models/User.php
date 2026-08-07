<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    public const ACCOUNT_TYPE_USER = 'user';

    public const ACCOUNT_TYPE_INSTRUCTOR = 'instructor';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'country',
        'city',
        'address',
        'account_type',
        'password',
        'marketing_email_opt_in',
        'marketing_email_opted_in_at',
        'marketing_email_unsubscribed_at',
        'marketing_email_consent_source',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'marketing_email_opt_in' => 'boolean',
            'marketing_email_opted_in_at' => 'datetime',
            'marketing_email_unsubscribed_at' => 'datetime',
        ];
    }

    public function mockTestHistories(): HasMany
    {
        return $this->hasMany(MockTestHistory::class);
    }

    public function isInstructor(): bool
    {
        return $this->account_type === self::ACCOUNT_TYPE_INSTRUCTOR;
    }

    public function scopeEligibleForMarketing(Builder $query): Builder
    {
        return $query
            ->where('marketing_email_opt_in', true)
            ->whereNotNull('email_verified_at')
            ->whereNull('marketing_email_unsubscribed_at');
    }
}
