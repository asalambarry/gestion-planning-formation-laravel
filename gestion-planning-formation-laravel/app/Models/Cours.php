<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'user_id',
        'formation_id',
    ];

    public function formation() {
        return $this->belongsTo(Formation::class, 'cours_id', 'id');
    }

    public function plannings() {
        return $this->hasMany(Planning::class, 'cours_id', 'id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'cours_users', 'cours_id', 'user_id');
    }

    public function enseignant() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
