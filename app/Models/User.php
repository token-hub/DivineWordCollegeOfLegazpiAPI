<?php

namespace App\Models;

use App\Jobs\ProcessEmailVerification;
use App\Jobs\ProcessResetPassword;
use App\Scopes\RolesScope;
use App\Traits\ActivityLog;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use DispatchesJobs;
    use ActivityLog;
    use CausesActivity;

    protected static $recordEvents = ['created', 'updated'];

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
        'is_active'
    ];

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
        'email_verified_at' => 'datetime:M d, Y h:i A',
        'updated_at' => 'datetime:M d, Y h:i A',
        'created_at' => 'datetime:M d, Y h:i A',
    ];

    // protected $with = ['roles'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new RolesScope());
    }

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
