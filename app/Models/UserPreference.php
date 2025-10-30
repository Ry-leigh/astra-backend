<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = ['user_id', 'notify_email', 'notify_in_app'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
