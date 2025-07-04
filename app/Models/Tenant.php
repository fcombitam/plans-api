<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tenant extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'tenant_code',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed'
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activePlan(): ?Subscription
    {
        return $this->subscriptions()->where('status', Subscription::STATUS_ACTIVE)->first();
    }
}
