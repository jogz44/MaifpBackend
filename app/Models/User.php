<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\role;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected static $logAttributes = ['name','text'];

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'position',
        'office',
        'status',
        'role_id',
        'username',
        'password',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'middle_name', 'position', 'office', 'status', 'role_id', 'username'])
            ->logOnlyDirty() // logs only changed attributes
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }



    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'password' => 'hashed',
    ];


     public function role(){

        return $this->belongsTo(role::class,);

     }
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Get the user's display name (first and last name only).
     */
    public function getDisplayNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Relationship with user credentials if you have a separate table
     */
    public function credentials()
    {
        return $this->hasMany(UserCredentials::class,'userid');
    }
}
