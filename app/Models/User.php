<?php

namespace App\Models;

use App\Jobs\ProcessEmailVerification;
use App\Jobs\ProcessResetPassword;
use App\Traits\ActivityLog;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use DispatchesJobs;
    use ActivityLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
    ];

    // protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->roles->pluck('description')->contains('admin');
    }

    public function setPasswordAttribute($value)
    {
        if (Hash::needsRehash($value)) {
            $value = Hash::make($value);
        }
        $this->attributes['password'] = $value;
    }

    public function sendPasswordResetNotification($token)
    {
        ProcessResetPassword::dispatch($this, $token);
    }

    public function sendEmailVerificationNotification()
    {
        ProcessEmailVerification::dispatch($this);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}
