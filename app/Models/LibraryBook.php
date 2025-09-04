<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LibraryBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'category_id',
        'title',
        'author',
        'isbn',
        'publisher',
        'publication_year',
        'edition',
        'description',
        'language',
        'pages',
        'price',
        'cover_image',
        'barcode',
        'total_copies',
        'available_copies',
        'issued_copies',
        'reserved_copies',
        'condition',
        'location',
        'is_active',
        'additional_info',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'price' => 'decimal:2',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'issued_copies' => 'integer',
        'reserved_copies' => 'integer',
        'is_active' => 'boolean',
        'additional_info' => 'array',
    ];

    // Boot method to generate barcode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->barcode)) {
                $book->barcode = 'BK' . date('Ymd') . strtoupper(Str::random(6));
            }
        });

        static::updating(function ($book) {
            // Ensure copy counts are consistent
            $book->issued_copies = $book->bookIssues()->where('status', 'issued')->count();
            $book->reserved_copies = $book->bookReservations()->where('status', 'active')->count();
            $book->available_copies = $book->total_copies - $book->issued_copies - $book->reserved_copies;
        });
    }

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }

    public function bookReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class, 'book_id');
    }

    public function currentIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id')->where('status', 'issued');
    }

    public function activeReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class, 'book_id')->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // Accessors & Mutators
    public function getIsAvailableAttribute(): bool
    {
        return $this->available_copies > 0 && $this->is_active;
    }

    public function getFullTitleAttribute(): string
    {
        return $this->title . ' by ' . $this->author;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    // Methods
    public function canBeIssued(): bool
    {
        return $this->is_available && $this->condition !== 'poor';
    }

    public function canBeReserved(): bool
    {
        return $this->is_active && $this->available_copies === 0;
    }

    public function updateCopyCounts(): void
    {
        $this->issued_copies = $this->bookIssues()->where('status', 'issued')->count();
        $this->reserved_copies = $this->bookReservations()->where('status', 'active')->count();
        $this->available_copies = $this->total_copies - $this->issued_copies - $this->reserved_copies;
        $this->save();
    }

    public function getPopularityScore(): int
    {
        return $this->bookIssues()->count() + ($this->bookReservations()->count() * 0.5);
    }
}
