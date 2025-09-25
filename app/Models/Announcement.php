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
}
