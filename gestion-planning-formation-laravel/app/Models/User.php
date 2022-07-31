<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'prenom',
        'login',
        'mdp',
        'formation_id',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'mdp',
    ];

    public $timestamps = false;

    public function isAdmin() {
        return $this->type === 'admin';
    }

    public function isEtudiant() {
        return $this->type === 'etudiant';
    }

    public function isEnseignant() {
        return $this->type === 'enseignant';
    }

    public function formation() {
        return $this->belongsTo(Formation::class, 'formation_id', 'id');
    }

    public function coursEnseignant() {
        return $this->hasMany(Cours::class, 'user_id', 'id');
    }

    public function cours() {
        return $this->belongsToMany(Cours::class, 'cours_users', 'user_id', 'cours_id');
    }

}
