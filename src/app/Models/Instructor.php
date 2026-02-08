<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_number',
        'document_type',
        'first_name',
        'last_name',
        'institutional_email',
        'phone',
        'executing_team_id',
        'specialty',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * -----------------------------
     * Relaciones 
     * -----------------------------
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function executingTeam()
    {
        return $this->belongsTo(ExecutingTeam::class);
    }

    public function norms()
    {
        return $this->belongsToMany(Norm::class, 'instructor_norm');
    }

    public function fichaCompetencyExecutions()
    {
        return $this->hasMany(FichaCompetencyExecution::class);
    }

    public function fichaInstructorLeaderships()
    {
        return $this->hasMany(FichaInstructorLeadership::class);
    }

    /**
     * -----------------------------
     * Accesores
     * -----------------------------
     */

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
