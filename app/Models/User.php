<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'city',
        'town',
        'province',
        'email',
        'password'];

    // Roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // If the user is an instructor
    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    // If the user is a student
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Announcements created
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

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
        ];
    }
}
