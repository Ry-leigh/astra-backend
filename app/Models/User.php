<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'address',
        'email',
        'password'];

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Roles
    public function roles()
    {
        return $this->belongsToMany(Role::class);
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

    public function markedAttendances() // for students
    {
        return $this->hasMany(AttendanceRecord::class, 'marked_by');
    }

    public function markedSessions() // for instructors
    {
        return $this->hasMany(ClassSession::class, 'marked_by');
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
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
