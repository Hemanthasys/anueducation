<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class SchoolBudgetExpenditure extends Model
{
    use Auditable;
    protected $table = 'school_budget_expenditure';

    protected $fillable = [
        'school_id',
        'academic_year',
        'expenditure_vote_id',
        'expected_amount',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
    ];

    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function expenditureVote(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExpenditureVote::class);
    }
}