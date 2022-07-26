<?php

namespace App\Models;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens;

    protected $attributes = [
        'gender' => UserGender::Unknown,
        'status' => UserStatus::Inactivated,
    ];

    protected $casts = [
        'gender' => UserGender::class,
        'birthday' => 'date',
        'status' => UserStatus::class,
    ];

    protected $fillable = [
        'nickname',
        'avatar',
        'gender',
        'birthday',
        'status',
    ];

    protected $hidden = [
        'remember_token',
    ];

    /**
     * 属于此用户的凭证
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function passport(): HasOne
    {
        return $this->hasOne(Passport::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthPassword()
    {
        return $this->passport->password;
    }
}
