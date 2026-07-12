<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class SchoolBudgetIncome extends Model
{
    use Auditable;
    protected $table = 'school_budget_income';

    protected $fillable = [
        'school_id',
        'academic_year',
        'funding_source_id',
        'expected_amount',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
    ];

    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function fundingSource(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FundingSource::class);
    }
}