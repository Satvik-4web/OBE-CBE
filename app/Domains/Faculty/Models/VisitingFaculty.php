<?php

namespace App\Domains\Faculty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Academic\Models\Program;
use App\Domains\Academic\Models\AcademicYear;

class VisitingFaculty extends Model
{
    protected $table = 'visiting_faculties';
    protected $guarded = [];

    protected $casts = [
        'interaction_hours' => 'float',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
