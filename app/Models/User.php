<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'date_of_birth',
        'gender',
        'status',
        'school_id',
        'last_panel_accessed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'status' => 'boolean',
        ];
    }

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    // Check if user is a super admin
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    // Check if user is a school admin
    public function isSchoolAdmin(): bool
    {
        return $this->hasRole('school_admin');
    }

    // Check if user is a student
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    // Check if user is a teacher
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    // Check if user is a parent
    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    // Check if user can access multiple schools (super admin)
    public function canAccessAllSchools(): bool
    {
        return $this->isSuperAdmin() && is_null($this->school_id);
    }

    // Get user's accessible schools
    public function getAccessibleSchools()
    {
        if ($this->canAccessAllSchools()) {
            return School::all();
        }

        return $this->school ? collect([$this->school]) : collect();
    }

    // Get children for parent users
    public function children()
    {
        return $this->hasMany(Student::class, 'parent_email', 'email');
    }

    /**
     * Update last accessed panel
     */
    public function updateLastAccessedPanel(string $panel): void
    {
        $this->update(['last_panel_accessed' => $panel]);
    }

    /**
     * Get appropriate panel redirect based on user role
     */
    public function getPanelRedirect(): string
    {
        if ($this->isSuperAdmin()) {
            return '/admin';
        }

        if ($this->hasAnyRole(['school_admin', 'principal'])) {
            return '/school';
        }

        if ($this->hasAnyRole(['teacher', 'librarian', 'accountant'])) {
            return '/faculty';
        }

        if ($this->isStudent()) {
            return '/student';
        }

        if ($this->isParent()) {
            return '/parent';
        }

        return '/admin'; // fallback
    }

    /**
     * Check if user can manage multiple schools
     */
    public function canManageMultipleSchools(): bool
    {
        return $this->isSuperAdmin();
    }
}
