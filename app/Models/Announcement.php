<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'event_date',
        'event_time'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targets()
    {
        return $this->hasMany(AnnouncementTarget::class);
    }

    public function pinnedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_pinned_announcements');
    }

    public function scopeVisibleTo($query, $user)
    {
        $roleNames = $user->roles->pluck('name')->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();

        if (in_array('Administrator', $roleNames)) {
            return $query;
        }

        return $query->whereHas('targets', function ($q) use ($user, $roleIds, $roleNames) {
            $q->where(function ($sub) use ($user, $roleIds, $roleNames) {
                $sub->orWhere(function ($q3) {
                    $q3->where('target_type', 'global')->whereNull('target_id');
                });
                $sub->orWhere(function ($q3) use ($roleIds) {
                    $q3->where('target_type', 'role')->whereIn('target_id', $roleIds);
                });
                if ($user->program_id) {
                    $sub->orWhere(function ($q3) use ($user) {
                        $q3->where('target_type', 'program')->where('target_id', $user->program_id);
                    });
                }
                if ($user->classroom_id) {
                    $sub->orWhere(function ($q3) use ($user) {
                        $q3->where('target_type', 'classroom')->where('target_id', $user->classroom_id);
                    });
                }
                if (in_array('Instructor', $roleNames) && $user->instructor && $user->instructor->classCourses) {
                    $courseIds = $user->instructor->classCourses->pluck('id')->toArray();
                    $sub->orWhere(function ($q3) use ($courseIds) {
                        $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                    });
                }
                if (array_intersect($roleNames, ['Student', 'Officer']) && $user->student && $user->student->enrollments) {
                    $courseIds = $user->student->enrollments->pluck('class_course_id')->toArray();
                    $sub->orWhere(function ($q3) use ($courseIds) {
                        $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                    });
                }
            });
        });
    }
}
