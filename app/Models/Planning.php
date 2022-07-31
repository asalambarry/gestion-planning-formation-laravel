<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'cours_id',
        'date_debut',
        'date_fin',
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
    ];

    public $timestamps = false;

    public function cours() {
        return $this->belongsTo(Cours::class, 'cours_id', 'id');
    }
}
