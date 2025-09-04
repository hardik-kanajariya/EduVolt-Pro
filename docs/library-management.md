# Phase 6: Library Management System

## Overview

The Library Management System is a comprehensive module that allows schools to manage their library inventory, track book issues and returns, handle reservations, manage fines, and generate detailed reports.

## Features

### 6.1 Library Inventory Management
- **Book Categories**: Organize books into categories with color coding
- **Library Books**: Complete book information with ISBN, author, publisher details
- **Inventory Tracking**: Track total copies and available copies
- **Barcode Generation**: Automatic barcode generation for each book
- **Search & Filter**: Advanced search and filtering capabilities

### 6.2 Book Issue & Return System
- **Issue Books**: Issue books to students with automatic due date calculation
- **Return Books**: Process book returns with fine calculation for overdue items
- **Renewal System**: Allow book renewals with configurable limits
- **Overdue Tracking**: Track and manage overdue books
- **Staff Actions**: Track which staff member issued/returned books

### 6.3 Library Reports & Analytics
- **Overview Dashboard**: Quick statistics and key metrics
- **Popular Books**: Track most borrowed books
- **Circulation Reports**: Book issue/return trends
- **Overdue Reports**: List of overdue books and students
- **Fine Reports**: Outstanding fines and payment tracking
- **Student Activity**: Active readers and borrowing patterns

### 6.4 Reservation System
- **Book Reservations**: Reserve books when all copies are issued
- **Queue Management**: First-come-first-served reservation queue
- **Automatic Expiry**: Reservations expire after configurable time
- **Notification System**: Alert students when reserved books become available

### 6.5 Fine Management
- **Automatic Fines**: Calculate fines for overdue returns
- **Payment Tracking**: Record fine payments
- **Fine Waivers**: Allow staff to waive fines with reasons
- **Fine Reports**: Track outstanding and paid fines

## Database Schema

### Book Categories
```sql
- id (Primary Key)
- name (VARCHAR, Required)
- description (TEXT, Optional)
- color (VARCHAR, Default: #3B82F6)
- sort_order (INTEGER, Default: 0)
- is_active (BOOLEAN, Default: true)
- school_id (Foreign Key to schools)
- created_at, updated_at, deleted_at
```

### Library Books
```sql
- id (Primary Key)
- title (VARCHAR, Required)
- author (VARCHAR, Required)
- isbn (VARCHAR, Unique)
- publisher (VARCHAR, Optional)
- publication_year (INTEGER, Optional)
- pages (INTEGER, Optional)
- language (VARCHAR, Default: 'English')
- description (TEXT, Optional)
- location (VARCHAR, Optional)
- barcode (VARCHAR, Unique, Auto-generated)
- total_copies (INTEGER, Default: 1)
- available_copies (INTEGER, Default: 1)
- school_id (Foreign Key to schools)
- book_category_id (Foreign Key to book_categories)
- created_at, updated_at, deleted_at
```

### Book Issues
```sql
- id (Primary Key)
- library_book_id (Foreign Key to library_books)
- student_id (Foreign Key to students)
- issued_by (Foreign Key to users)
- returned_to (Foreign Key to users, Nullable)
- issued_at (TIMESTAMP)
- due_date (DATE)
- returned_at (TIMESTAMP, Nullable)
- renewal_count (INTEGER, Default: 0)
- notes (TEXT, Optional)
- created_at, updated_at
```

### Book Reservations
```sql
- id (Primary Key)
- library_book_id (Foreign Key to library_books)
- student_id (Foreign Key to students)
- reserved_by (Foreign Key to users)
- fulfilled_by (Foreign Key to users, Nullable)
- reserved_at (TIMESTAMP)
- expires_at (TIMESTAMP)
- fulfilled_at (TIMESTAMP, Nullable)
- status (ENUM: pending, fulfilled, expired, cancelled)
- notes (TEXT, Optional)
- created_at, updated_at
```

### Library Fines
```sql
- id (Primary Key)
- book_issue_id (Foreign Key to book_issues)
- student_id (Foreign Key to students)
- amount (DECIMAL, Precision: 8,2)
- reason (VARCHAR)
- status (ENUM: unpaid, paid, waived)
- fine_date (DATE)
- paid_at (TIMESTAMP, Nullable)
- paid_by (Foreign Key to users, Nullable)
- waived_at (TIMESTAMP, Nullable)
- waived_by (Foreign Key to users, Nullable)
- waiver_reason (TEXT, Nullable)
- created_at, updated_at
```

## Models and Relationships

### LibraryBook Model
- Belongs to School
- Belongs to BookCategory
- Has many BookIssues
- Has many BookReservations
- Methods: `isAvailable()`, `borrowBook()`, `returnBook()`, `getPopularityScore()`

### BookIssue Model
- Belongs to LibraryBook
- Belongs to Student
- Belongs to User (issued_by, returned_to)
- Has one LibraryFine
- Methods: `isOverdue()`, `getOverdueDays()`, `renewBook()`, `calculateFine()`

### BookCategory Model
- Belongs to School
- Has many LibraryBooks
- Scopes: `active()`
- Accessors: `getBooksCountAttribute()`

### BookReservation Model
- Belongs to LibraryBook
- Belongs to Student
- Belongs to User (reserved_by, fulfilled_by)
- Methods: `fulfill()`, `expire()`, `cancel()`

### LibraryFine Model
- Belongs to BookIssue
- Belongs to Student
- Belongs to User (paid_by, waived_by)
- Scopes: `unpaid()`, `paid()`, `waived()`
- Methods: `markAsPaid()`, `waiveFine()`

## Filament Resources

### Admin Panel Resources
- BookCategoryResource
- LibraryBookResource
- BookIssueResource
- BookReservationResource
- LibraryFineResource

### Custom Pages
- LibraryReports (Comprehensive reporting dashboard)
- QuickIssue (Fast book issuing interface)
- QuickReturn (Fast book return interface)

### Widgets
- ReadingStatsWidget (Key library statistics)
- PopularBooksWidget (Chart of popular books)
- OverdueBooksWidget (List of overdue items)

## Configuration

### Library Settings
```php
// config/library.php
return [
 'default_loan_period' => 14, // days
 'max_renewals' => 3,
 'renewal_period' => 14, // days
 'overdue_fine_per_day' => 1.00, // dollars
 'max_fine_amount' => 50.00, // dollars
 'reservation_expiry_days' => 7,
 'max_books_per_student' => 5,
];
```

### Permissions
- `view-library-books`
- `create-library-books`
- `edit-library-books`
- `delete-library-books`
- `issue-books`
- `return-books`
- `manage-reservations`
- `manage-fines`
- `view-library-reports`

## Usage Examples

### Issue a Book
```php
$issue = BookIssue::create([
 'library_book_id' => $bookId,
 'student_id' => $studentId,
 'issued_by' => auth()->id(),
 'issued_at' => now(),
]);

// Update book availability
$book = LibraryBook::find($bookId);
$book->borrowBook();
```

### Return a Book
```php
$issue = BookIssue::find($issueId);
$issue->update([
 'returned_at' => now(),
 'returned_to' => auth()->id(),
]);

// Update book availability
$issue->libraryBook->returnBook();

// Create fine if overdue
if ($issue->isOverdue()) {
 LibraryFine::create([
 'book_issue_id' => $issue->id,
 'student_id' => $issue->student_id,
 'amount' => $issue->calculateFine(),
 'reason' => 'Overdue return',
 'status' => 'unpaid',
 'fine_date' => now(),
 ]);
}
```

### Generate Reports
```php
// Popular books
$popularBooks = LibraryBook::withCount('bookIssues')
 ->orderByDesc('book_issues_count')
 ->limit(10)
 ->get();

// Overdue books
$overdueBooks = BookIssue::with(['libraryBook', 'student'])
 ->whereNull('returned_at')
 ->where('due_date', '<', now())
 ->get();

// Outstanding fines
$outstandingFines = LibraryFine::with(['student', 'bookIssue.libraryBook'])
 ->where('status', 'unpaid')
 ->sum('amount');
```

## Testing

Comprehensive test suite includes:
- Unit tests for all models
- Feature tests for book operations
- Integration tests for complete workflows
- Fine calculation and payment tests
- Reservation system tests

Run tests with:
```bash
php artisan test tests/Feature/Library/
```

## Installation and Setup

1. Run migrations:
```bash
php artisan migrate
```

2. Seed sample data:
```bash
php artisan db:seed --class=LibrarySeeder
```

3. Configure permissions in Filament admin panel

4. Customize library settings in config file

## Future Enhancements

- Digital book management
- Library card generation
- Email notifications for due dates
- Mobile app integration
- Advanced reporting with charts
- Inventory audit tools
- Book recommendation system
- Integration with external library systems
