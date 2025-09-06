<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check() && Auth::user()->school_id) {
            $builder->where($model->getTable() . '.school_id', Auth::user()->school_id);
        }
    }
}

trait HasSchoolScope
{
    /**
     * Boot the trait and add the global scope.
     */
    protected static function bootHasSchoolScope(): void
    {
        static::addGlobalScope(new SchoolScope);
    }

    /**
     * Get a query builder that ignores the school scope.
     */
    public static function withoutSchoolScope(): Builder
    {
        return static::withoutGlobalScope(SchoolScope::class);
    }

    /**
     * Scope a query to a specific school.
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Check if the current user can access all schools.
     */
    public static function canAccessAllSchools(): bool
    {
        return Auth::check() &&
            Auth::user()->hasRole('super_admin') &&
            is_null(Auth::user()->school_id);
    }
}
