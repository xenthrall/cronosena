<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingLevel extends Model
{
    protected $table = 'training_levels';

    protected $fillable = [
        'name',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
