# Filament v4 Complete Migration & Core Functionalities Guide

## Requirements & Setup

### New Requirements
- **PHP 8.2+**
- **Laravel v11.28+**
- **Tailwind CSS v4.0+** (if using custom themes)
- Remove `doctrine/dbal` dependency (no longer required)

### Installation & Upgrade Process
```bash
# Automated upgrade script
composer require filament/upgrade:"^4.0" -W --dev
vendor/bin/filament-v4

# Follow script output commands
composer require filament/filament:"^4.0" -W --no-update
composer update

# Optional: Upgrade directory structure
php artisan filament:upgrade-directory-structure-to-v4 --dry-run
php artisan filament:upgrade-directory-structure-to-v4

# Cleanup
composer remove filament/upgrade --dev
```

## Major Breaking Changes & Migration

### 1. Schema System (Unified Components)
**v3 Syntax:**
```php
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

// Separate namespaces for forms and infolists
```

**v4 Syntax:**
```php
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

// Unified namespace - can mix forms and infolists
$schema->components([
Grid::make(2)->schema([
Section::make('Details')->schema([
TextInput::make('name'),
Select::make('position'),
]),
Section::make('Auditing')->schema([
TextEntry::make('created_at')->dateTime(),
TextEntry::make('updated_at')->dateTime(),
]),
])
]);
```

### 2. Unified Actions
**v3 Syntax:**
```php
use Filament\Tables\Actions\Action;
use Filament\Forms\Actions\Action; // Wrong import!
use Filament\Infolists\Actions\Action; // Multiple Action classes
```

**v4 Syntax:**
```php
use Filament\Actions\Action;

// Single Action class for all contexts
// Reusable across Tables, Forms, Infolists, etc.
```

### 3. New Directory Structure
**v3 Structure:**
```
app/Filament/Resources/
├── UserResource.php
└── UserResource/
└── Pages/
```

**v4 Structure:**
```
app/Filament/Resources/
└── UserResource/
├── UserResource.php
├── Schemas/
│ ├── UserFormSchema.php
│ └── UserInfolistSchema.php
├── Tables/
│ └── UserTable.php
└── Pages/
```

### 4. Resource Structure Changes
**v3 Resource:**
```php
class UserResource extends Resource
{
public static function form(Form $form): Form
{
return $form->schema([
TextInput::make('name'),
]);
}

public static function table(Table $table): Table
{
return $table->columns([
TextColumn::make('name'),
]);
}
}
```

**v4 Resource (New Structure):**
```php
class UserResource extends Resource
{
public static function form(Form $form): Form
{
return $form->schema(UserFormSchema::make());
}

public static function table(Table $table): Table
{
return UserTable::make($table);
}
}

// Separate Schema Class
class UserFormSchema
{
public static function make(): array
{
return [
TextInput::make('name'),
];
}
}

// Separate Table Class
class UserTable
{
public static function make(Table $table): Table
{
return $table->columns([
TextColumn::make('name'),
]);
}
}
```

## Core Feature Changes

### 1. Custom Data Tables
**v4 New Feature:**
```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
return $table
->records([
['name' => 'John', 'email' => 'john@example.com'],
['name' => 'Jane', 'email' => 'jane@example.com'],
])
->columns([
TextColumn::make('name'),
TextColumn::make('email'),
]);
}
```

### 2. Nested Resources
**v4 New Feature:**
```bash
# Generate nested resource
php artisan make:filament-resource LessonResource --nested=CourseResource
```

```php
// Nested resource URLs: /courses/{course}/lessons/{lesson}
class LessonResource extends Resource
{
protected static ?string $parentResource = CourseResource::class;
}
```

### 3. Multi-Factor Authentication
**v4 Built-in MFA:**
```php
// In Panel configuration
->mfa()
->mfaMethods(['google2fa', 'email'])
```

### 4. Performance Improvements
**v4 Partial Rendering:**
```php
// Skip unnecessary re-renders
TextInput::make('name')
->afterStateUpdated(fn() => null)
->partiallyRenderComponentsAfterStateUpdated()
->skipRenderAfterStateUpdated();
```

## Method & Syntax Changes

### Table Actions
**v3:**
```php
->actions([...])
```
**v4:**
```php
->recordActions([...])
```

### Layout Components Spanning
**v3 (default full width):**
```php
Section::make() // Spans full width by default
```
**v4 (single column by default):**
```php
Section::make()->columnSpanFull() // Must specify full width
```

### Unique Validation
**v3:**
```php
->unique() // Doesn't ignore current record
```
**v4:**
```php
->unique() // Ignores current record by default
->unique(ignoreRecord: false) // To disable new behavior
```

### Filter Behavior
**v3:**
```php
// Filters applied immediately
```
**v4:**
```php
// Filters deferred by default (need to click Apply)
->deferFilters(false) // To disable deferred behavior
```

### File Visibility
**v3:**
```php
FileUpload::make() // Public by default
```
**v4:**
```php
FileUpload::make() // Private by default for non-local disks
->visibility('public') // To make public
```

### Enum Field State
**v3:**
```php
// Returns either enum value or instance inconsistently
```
**v4:**
```php
// Always returns enum instance
Select::make('status')
->options(Status::class)
->afterStateUpdated(function (?Status $state) {
// $state is always Status instance or null
});
```

## Configuration Changes

### Tailwind CSS v4 Theme
**v3 Theme File:**
```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';
@config 'tailwind.config.js';
```

**v4 Theme File:**
```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';
@source '../../../../app/Filament';
@source '../../../../resources/views/filament';
```

### Configuration Reversions
```php
// config/filament.php - To preserve v3 behaviors
return [
'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

'file_generation' => [
'flags' => [
FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS,
FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES,
FileGenerationFlag::PARTIAL_IMPORTS,
],
],
];
```

### Global Behavior Reversions
```php
// AppServiceProvider::boot()
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;

// Preserve v3 filter behavior
Table::configureUsing(fn (Table $table) => $table->deferFilters(false));

// Preserve v3 file visibility
FileUpload::configureUsing(fn (FileUpload $fileUpload) => $fileUpload->visibility('public'));

// Preserve v3 layout spanning
Section::configureUsing(fn (Section $section) => $section->columnSpanFull());
```

## URL Parameter Changes
- `activeRelationManager` → `relation`
- `activeTab` → `tab`
- `isTableReordering` → `reordering`
- `tableFilters` → `filters`
- `tableGrouping` → `grouping`
- `tableSearch` → `search`
- `tableSort` → `sort`

## New Components & Features

### New Form Fields
```php
// TipTap Rich Editor (replaces Trix)
RichEditor::make('content')
->blocks([...])
->mergeTags([...]);

// Slider
Slider::make('rating')->min(1)->max(5);

// Code Editor
CodeEditor::make('config');

// Table Repeater
TableRepeater::make('items');
```

### Client-Side JS Helpers
```php
TextInput::make('name')
->hiddenJs(fn (Get $get) => $get('type') === 'hidden')
->visibleJs(fn (Get $get) => $get('type') === 'visible')
->afterStateUpdatedJs('console.log("Updated")');
```

### Tenancy Improvements
- Automatic global scoping for all queries
- Automatic tenant association for new records
- No manual scoping required in most cases

## Performance Features
- **3x faster table rendering** for large datasets
- **Partial rendering** support
- **Client-side validation** improvements
- **Reduced HTML output** for tables
- **Optimized Blade component usage**

## Critical Development Rules

### 1. Component State Management
```php
// RULE: Always use reactive() for dependent fields
Select::make('country_id')
->reactive() // v3 style
->live() // v4 preferred - more performant
->afterStateUpdated(fn (callable $set) => $set('state_id', null));

// RULE: Use lazy() for heavy operations
Select::make('user_id')
->searchable()
->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
->lazy(); // Only load when field is focused
```

### 2. Query Performance Rules
```php
// RULE: Always optimize N+1 queries in tables
public static function table(Table $table): Table
{
return $table
->modifyQueryUsing(fn (Builder $query) => $query->with('category', 'author'))
->columns([
TextColumn::make('category.name'), // Won't cause N+1
TextColumn::make('author.name'), // Won't cause N+1
]);
}

// RULE: Use deferLoading for heavy data
public static function table(Table $table): Table
{
return $table->deferLoading(); // Shows skeleton while loading
}
```

### 3. Security Rules
```php
// RULE: Always authorize resource access
class UserResource extends Resource
{
public static function canViewAny(): bool
{
return auth()->user()->can('view_users');
}

public static function canCreate(): bool
{
return auth()->user()->can('create_users');
}
}

// RULE: Validate file uploads strictly
FileUpload::make('avatar')
->acceptedFileTypes(['image/jpeg', 'image/png'])
->maxSize(2048) // KB
->image()
->imageResizeMode('cover')
->imageCropAspectRatio('1:1');
```

### 4. Form Validation Rules
```php
// RULE: Use closure validation for complex rules
TextInput::make('email')
->email()
->rules([
fn (): Closure => function (string $attribute, $value, Closure $fail) {
if (User::where('email', $value)->where('id', '!=', request()->route('record'))->exists()) {
$fail('The email has already been taken.');
}
},
]);

// RULE: Group related validations
Group::make([
TextInput::make('password')->password()->required(),
TextInput::make('password_confirmation')->password()->same('password'),
])->relationship('user');
```

## Database & Eloquent Rules

### 5. Model Preparation
```php
// RULE: Always use proper casting in models
class Post extends Model
{
protected $casts = [
'published_at' => 'datetime',
'settings' => 'array',
'is_featured' => 'boolean',
'price' => 'decimal:2',
];

// RULE: Define searchable attributes
public function getFilamentSearchColumns(): array
{
return ['title', 'content', 'author.name'];
}
}

// RULE: Use proper relationships
public function category(): BelongsTo
{
return $this->belongsTo(Category::class);
}
```

### 6. Migration Rules
```php
// RULE: Always add proper indexes for Filament tables
Schema::create('posts', function (Blueprint $table) {
$table->id();
$table->string('title')->index(); // For search
$table->foreignId('category_id')->constrained()->cascadeOnDelete();
$table->enum('status', ['draft', 'published'])->index(); // For filtering
$table->timestamp('published_at')->nullable()->index(); // For sorting
$table->timestamps();
});
```

## UI/UX Best Practices

### 7. User Experience Rules
```php
// RULE: Provide helpful placeholders and hints
TextInput::make('slug')
->placeholder('post-title-slug')
->helperText('URL-friendly version of the title')
->hint('Auto-generated from title if left empty');

// RULE: Use proper loading states
Select::make('category_id')
->relationship('category', 'name')
->searchable()
->preload() // Load options immediately
->loadingMessage('Loading categories...')
->noSearchResultsMessage('No categories found.')
->searchPrompt('Search categories...');
```

### 8. Navigation & Organization
```php
// RULE: Organize resources logically
protected static ?string $navigationGroup = 'Content Management';
protected static ?int $navigationSort = 1;
protected static ?string $navigationLabel = 'Blog Posts';

// RULE: Use proper icons (Heroicons)
protected static ?string $navigationIcon = 'heroicon-o-document-text';

// RULE: Implement breadcrumbs for nested resources
public static function getBreadcrumb(): string
{
return static::getModelLabel();
}
```

## Error Handling & Debugging

### 9. Error Prevention Rules
```php
// RULE: Handle null values gracefully
TextColumn::make('author.name')
->default('No Author')
->description(fn ($record) => $record->author?->email);

// RULE: Use try-catch for risky operations
Select::make('status')
->options(function () {
try {
return StatusEnum::cases();
} catch (\Exception $e) {
return ['active' => 'Active', 'inactive' => 'Inactive'];
}
});
```

### 10. Debugging Rules
```php
// RULE: Add debug helpers in development
TextInput::make('debug_info')
->visible(app()->environment('local'))
->default(fn ($record) => json_encode($record->toArray(), JSON_PRETTY_PRINT));

// RULE: Use meaningful variable names
public static function form(Form $form): Form
{
return $form->schema([
Section::make('User Details')
->description('Basic information about the user')
->schema(self::getUserDetailsSchema()),
]);
}
```

## Performance Optimization

### 11. Caching Rules
```php
// RULE: Cache expensive operations
Select::make('popular_tags')
->options(
Cache::remember('popular_tags', 3600, fn () =>
Tag::popular()->pluck('name', 'id')->toArray()
)
);

// RULE: Use database transactions for complex operations
protected function handleRecordCreation(array $data): Model
{
return DB::transaction(function () use ($data) {
$user = User::create($data);
$user->assignRole('customer');
return $user;
});
}
```

### 12. Resource Optimization
```php
// RULE: Implement proper pagination
public static function table(Table $table): Table
{
return $table
->defaultPaginationPageOption(25)
->paginationPageOptions([10, 25, 50, 100]);
}

// RULE: Use bulk operations where possible
->bulkActions([
BulkActionGroup::make([
DeleteBulkAction::make(),
BulkAction::make('activate')
->action(fn (Collection $records) => $records->each->activate()),
]),
]);
```

## Code Organization & Maintainability

### 13. Structure Rules
```php
// RULE: Extract complex schemas to separate classes
class UserFormSchema
{
public static function make(): array
{
return [
self::getPersonalDetailsSection(),
self::getContactInformationSection(),
self::getPreferencesSection(),
];
}

private static function getPersonalDetailsSection(): Section
{
return Section::make('Personal Details')->schema([
TextInput::make('first_name')->required(),
TextInput::make('last_name')->required(),
]);
}
}

// RULE: Use enums for consistent options
enum UserStatus: string
{
case Active = 'active';
case Inactive = 'inactive';
case Suspended = 'suspended';

public function getLabel(): string
{
return match($this) {
self::Active => 'Active',
self::Inactive => 'Inactive',
self::Suspended => 'Suspended',
};
}
}
```

### 14. Testing Rules
```php
// RULE: Write feature tests for resources
public function test_can_create_user(): void
{
$this->actingAs(User::factory()->admin()->create());

livewire(CreateUser::class)
->fillForm([
'name' => 'John Doe',
'email' => 'john@example.com',
])
->call('create')
->assertHasNoFormErrors();

$this->assertDatabaseHas('users', ['email' => 'john@example.com']);
}
```

## Multi-tenancy & Permissions

### 15. Tenancy Rules
```php
// RULE: Implement proper tenant scoping
class PostResource extends Resource
{
public static function getEloquentQuery(): Builder
{
return parent::getEloquentQuery()
->whereBelongsTo(Filament::getTenant());
}
}

// RULE: Use policy methods for authorization
class PostPolicy
{
public function viewAny(User $user): bool
{
return $user->can('view_posts');
}

public function create(User $user): bool
{
return $user->can('create_posts');
}
}
```

## Configuration & Environment

### 16. Environment Rules
```php
// RULE: Use environment-specific configurations
// .env
FILAMENT_FILESYSTEM_DISK=s3
FILAMENT_DEFAULT_AVATAR_PROVIDER=ui-avatars
MAIL_MAILER=smtp

// config/filament.php
'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'local'),
'avatars' => [
'provider' => env('FILAMENT_DEFAULT_AVATAR_PROVIDER', 'ui-avatars'),
],
```

### 17. Plugin Development Rules
```php
// RULE: Follow plugin structure
class MyPlugin implements Plugin
{
public function getId(): string
{
return 'my-plugin';
}

public function register(Panel $panel): void
{
return $panel
->resources([MyResource::class])
->pages([MyPage::class]);
}
}

// RULE: Use proper service provider
class MyPluginServiceProvider extends ServiceProvider
{
public function boot(): void
{
$this->loadViewsFrom(__DIR__.'/../resources/views', 'my-plugin');
}
}
```

# Filament v4 Developer Tips & Tricks Guide

## Quick Development Tips

### 1. Artisan Command Shortcuts
```bash
# Quick resource generation with everything
php artisan make:filament-resource Post --generate --view

# Generate with specific panel
php artisan make:filament-resource Post --panel=admin

# Create nested resource
php artisan make:filament-resource Comment --nested=PostResource

# Generate widget with chart
php artisan make:filament-widget StatsOverview --stats-overview

# Create custom page with navigation
php artisan make:filament-page Settings --type=custom
```

### 2. Smart Defaults & Auto-Configuration
```php
// TRICK: Auto-generate slug from title
TextInput::make('title')
    ->live(onBlur: true)
    ->afterStateUpdated(fn (Set $set, ?string $state) => 
        $set('slug', Str::slug($state))
    );

// TRICK: Auto-fill related fields
Select::make('user_id')
    ->relationship('user', 'name')
    ->live()
    ->afterStateUpdated(function (Set $set, ?int $state) {
        if ($state) {
            $user = User::find($state);
            $set('email', $user->email);
            $set('phone', $user->phone);
        }
    });
```

### 3. Conditional Field Display
```php
// TRICK: Hide/show fields based on other field values
Radio::make('type')
    ->options([
        'individual' => 'Individual',
        'company' => 'Company',
    ])
    ->live();

TextInput::make('company_name')
    ->visible(fn (Get $get) => $get('type') === 'company');

TextInput::make('tax_id')
    ->visible(fn (Get $get) => $get('type') === 'company');
```

## Form Field Magic Tricks

### 4. Advanced Field Configurations
```php
// TRICK: Multi-step form with progress indicator
Wizard::make([
    Step::make('Basic Info')
        ->schema([...])
        ->icon('heroicon-o-user'),
    Step::make('Details')
        ->schema([...])
        ->icon('heroicon-o-document-text'),
]);

// TRICK: Repeater with preset templates
Repeater::make('blocks')
    ->schema([
        Select::make('type')
            ->options([
                'text' => 'Text Block',
                'image' => 'Image Block',
            ])
            ->live(),
        Textarea::make('content')
            ->visible(fn (Get $get) => $get('type') === 'text'),
        FileUpload::make('image')
            ->visible(fn (Get $get) => $get('type') === 'image'),
    ])
    ->addActionLabel('Add Block')
    ->reorderableWithButtons()
    ->collapsible();
```

### 5. Smart Validation Patterns
```php
// TRICK: Custom validation with user feedback
TextInput::make('username')
    ->rules(['required', 'min:3', 'max:20', 'alpha_dash'])
    ->validationMessages([
        'alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores.',
    ])
    ->suffixAction(
        Action::make('check-availability')
            ->icon('heroicon-m-magnifying-glass')
            ->action(function (Get $get, Set $set) {
                $username = $get('username');
                $available = !User::where('username', $username)->exists();
                $set('username_available', $available);
            })
    );

// TRICK: Password strength indicator
TextInput::make('password')
    ->password()
    ->revealable()
    ->helperText(fn (?string $state): string => match (true) {
        strlen($state ?? '') < 8 => '⚠️ Too short',
        !preg_match('/[A-Z]/', $state ?? '') => '⚠️ Add uppercase letter',
        !preg_match('/[0-9]/', $state ?? '') => '⚠️ Add number',
        default => '✅ Strong password'
    })
    ->live(onBlur: true);
```

## Table Enhancement Tricks

### 6. Smart Table Configurations
```php
// TRICK: Custom table states
public static function table(Table $table): Table
{
    return $table
        ->emptyStateHeading('No posts yet')
        ->emptyStateDescription('Once you write your first post, it will appear here.')
        ->emptyStateActions([
            Action::make('create')
                ->label('Create first post')
                ->url(static::getUrl('create'))
                ->icon('heroicon-m-plus')
                ->button(),
        ]);
}

// TRICK: Advanced column formatting
TextColumn::make('price')
    ->money('USD')
    ->sortable()
    ->color(fn ($state) => $state > 100 ? 'success' : 'warning');

TextColumn::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'gray',
        'reviewing' => 'warning',
        'published' => 'success',
        'rejected' => 'danger',
    })
    ->icon(fn (string $state): string => match ($state) {
        'draft' => 'heroicon-m-pencil',
        'reviewing' => 'heroicon-m-eye',
        'published' => 'heroicon-m-check',
        'rejected' => 'heroicon-m-x-mark',
    });
```

### 7. Interactive Table Features
```php
// TRICK: Inline editing
Tables\Columns\TextInputColumn::make('title')
    ->rules(['required', 'max:255']);

Tables\Columns\SelectColumn::make('status')
    ->options([
        'draft' => 'Draft',
        'published' => 'Published',
    ]);

// TRICK: Quick actions
Tables\Actions\Action::make('clone')
    ->icon('heroicon-m-document-duplicate')
    ->action(fn ($record) => $record->replicate()->save())
    ->requiresConfirmation()
    ->modalHeading('Clone this record?')
    ->modalDescription('This will create an exact copy.')
    ->modalSubmitActionLabel('Clone it');
```

## Widget & Dashboard Magic

### 8. Smart Widget Patterns
```php
// TRICK: Real-time updating stats
class LiveStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        return [
            Stat::make('Online Users', User::whereOnline()->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Mini chart
        ];
    }
    
    protected function getPollingInterval(): ?string
    {
        return '5s'; // Auto-refresh every 5 seconds
    }
}

// TRICK: Interactive chart with filters
class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Overview';
    protected static ?int $sort = 2;
    
    public ?string $filter = 'today';
    
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
    
    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        return match($activeFilter) {
            'today' => $this->getTodayData(),
            'week' => $this->getWeekData(),
            // ... more cases
        };
    }
}
```

## Advanced Component Patterns

### 9. Custom Field Components
```php
// TRICK: Create reusable field groups
class AddressFieldGroup
{
    public static function make(string $prefix = ''): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make($prefix . 'street')
                    ->label('Street Address')
                    ->required(),
                TextInput::make($prefix . 'city')
                    ->label('City')
                    ->required(),
            ]),
            Grid::make(3)->schema([
                TextInput::make($prefix . 'state')
                    ->label('State')
                    ->required(),
                TextInput::make($prefix . 'zip')
                    ->label('ZIP Code')
                    ->required(),
                Select::make($prefix . 'country')
                    ->label('Country')
                    ->options(Country::pluck('name', 'code'))
                    ->required(),
            ]),
        ];
    }
}

// Usage in forms
Section::make('Billing Address')
    ->schema(AddressFieldGroup::make('billing_'));
```

### 10. Dynamic Form Builders
```php
// TRICK: Build forms from configuration
class DynamicFormBuilder
{
    public static function buildFromConfig(array $config): array
    {
        return collect($config['fields'])
            ->map(function ($field) {
                return match($field['type']) {
                    'text' => TextInput::make($field['name'])
                        ->label($field['label'])
                        ->required($field['required'] ?? false),
                    'select' => Select::make($field['name'])
                        ->label($field['label'])
                        ->options($field['options'])
                        ->required($field['required'] ?? false),
                    'file' => FileUpload::make($field['name'])
                        ->label($field['label'])
                        ->acceptedFileTypes($field['accept'] ?? [])
                        ->required($field['required'] ?? false),
                };
            })
            ->toArray();
    }
}
```

## Performance & UX Enhancements

### 11. Loading & Performance Tricks
```php
// TRICK: Skeleton loading for heavy tables
public static function table(Table $table): Table
{
    return $table
        ->deferLoading() // Show skeleton while loading
        ->recordUrl(null) // Disable row clicking for performance
        ->extremePagination(); // Better for large datasets
}

// TRICK: Lazy loading for expensive operations
Select::make('category_id')
    ->relationship('category', 'name')
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => 
        Category::where('name', 'like', "%{$search}%")
            ->limit(50)
            ->pluck('name', 'id')
            ->toArray()
    )
    ->getOptionLabelUsing(fn ($value): ?string => 
        Category::find($value)?->name
    );
```

### 12. User Experience Enhancements
```php
// TRICK: Progressive disclosure
Section::make('Advanced Options')
    ->schema([...])
    ->collapsed() // Start collapsed
    ->persistCollapsed(); // Remember state

// TRICK: Smart form navigation
Wizard::make([...])
    ->nextAction(
        fn (Action $action) => $action->label('Continue →')
    )
    ->previousAction(
        fn (Action $action) => $action->label('← Go Back')
    )
    ->submitAction(new HtmlString(Blade::render(<<<BLADE
        <x-filament::button
            type="submit"
            size="sm"
        >
            Create Post 🚀
        </x-filament::button>
    BLADE)));
```

## Debugging & Development Helpers

### 13. Debug Utilities
```php
// TRICK: Debug panel in development
public static function form(Form $form): Form
{
    $schema = [...];
    
    if (app()->environment('local')) {
        $schema[] = Section::make('Debug Info')
            ->schema([
                Placeholder::make('debug')
                    ->content(fn (?Model $record) => 
                        new HtmlString('<pre>' . json_encode($record?->toArray(), JSON_PRETTY_PRINT) . '</pre>')
                    ),
            ])
            ->collapsed();
    }
    
    return $form->schema($schema);
}

// TRICK: Query debugging
public static function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(function (Builder $query) {
            if (app()->environment('local') && request()->has('debug')) {
                dump($query->toSql(), $query->getBindings());
            }
            return $query;
        });
}
```

### 14. Rapid Prototyping Helpers
```php
// TRICK: Quick admin user creation
// In DatabaseSeeder.php
User::factory()->create([
    'name' => 'Admin',
    'email' => 'admin@admin.com',
    'password' => Hash::make('admin'),
])->assignRole('super-admin');

// TRICK: Auto-generate demo data
class PostResource extends Resource
{
    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('generate_demo')
                    ->label('Generate Demo Data')
                    ->visible(app()->environment('local'))
                    ->action(fn () => Post::factory(10)->create())
                    ->requiresConfirmation(),
            ]);
    }
}
```

### 15. Custom Themes & Styling Shortcuts
```php
// TRICK: Quick theme customization
// In AppServiceProvider::boot()
FilamentColor::register([
    'primary' => Color::Amber,
    'gray' => Color::Slate,
]);

// TRICK: Custom CSS classes
TextInput::make('title')
    ->extraAttributes([
        'class' => 'font-bold text-lg',
        'style' => 'border-color: #10b981;'
    ]);
```

## Common Gotchas & Solutions

### 16. Troubleshooting Tips
```php
// ISSUE: Relationship not loading
// SOLUTION: Use modifyQueryUsing
public static function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with(['category', 'author']));
}

// ISSUE: File uploads not working
// SOLUTION: Check disk configuration
FileUpload::make('image')
    ->disk('public') // Specify disk explicitly
    ->directory('uploads/images')
    ->visibility('public'); // For public access

// ISSUE: Custom validation not working
// SOLUTION: Use proper closure syntax
TextInput::make('username')
    ->rule('required')
    ->rule(function () {
        return function (string $attribute, $value, Closure $fail) {
            if (User::where('username', $value)->exists()) {
                $fail('Username already taken.');
            }
        };
    });
```

These tips and tricks provide practical shortcuts and patterns that make Filament v4 development more efficient and enjoyable, covering everything from quick commands to advanced customization patterns.