# Hector CMS

A modern, flexible Content Management System built with the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) inspired by Statamic CMS.

## Features

- **Collections**: Organize your content into logical groups
- **Blueprints**: Define custom content structures with flexible field types
- **Entries**: Create and manage content with dynamic fields
- **User Management**: Role-based access control (Admin, Editor, Viewer)
- **Bulk Actions**: Perform actions on multiple entries at once
- **Entry Preview**: View entries before publishing
- **Audit Logging**: Track all content changes with timestamps and user attribution
- **Two-Factor Authentication**: Enhanced security for user accounts

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire 3, Alpine.js 3
- **UI Components**: Flux UI Pro
- **CSS**: Tailwind CSS 4
- **Database**: SQLite (easily swappable to MySQL/PostgreSQL)
- **Testing**: Pest 4
- **Code Quality**: Laravel Pint, Larastan, Rector

## Installation

### Prerequisites

- PHP 8.4+
- Composer
- Node.js & NPM
- Laravel Herd (recommended) or Valet/Homestead

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd hector
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed the database** (optional)
   ```bash
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

8. **Serve the application**
   - Using Laravel Herd: Automatically available at `https://hector.test`
   - Using Artisan: `php artisan serve`

## User Roles & Permissions

### Admin
- Full access to all features
- Can manage users, collections, blueprints, and entries
- Can view activity logs
- Can perform bulk operations

### Editor
- Can create, edit, and delete entries
- Can manage collections and blueprints
- Cannot manage users or view user settings

### Viewer
- Read-only access to entries
- Cannot create, edit, or delete content
- Cannot access admin features

## Core Concepts

### Collections

Collections are containers that organize your content. Each collection represents a type of content (e.g., Blog Posts, Products, Team Members).

**Creating a Collection:**
1. Navigate to **Content > Collections**
2. Click **Create Collection**
3. Enter a name (e.g., "Blog Posts")
4. Choose a blueprint that defines the content structure
5. Click **Save**

### Blueprints

Blueprints define the structure of your content by specifying fields and their types.

**Available Field Types:**
- `text` - Single-line text input
- `textarea` - Multi-line text area
- `richtext` - Rich text editor
- `email` - Email address
- `url` - Website URL
- `number` - Numeric input
- `date` - Date picker
- `datetime` - Date and time picker
- `checkbox` - Boolean checkbox
- `select` - Dropdown selection
- `radio` - Radio button selection

**Creating a Blueprint:**
1. Navigate to **Content > Blueprints**
2. Click **Create Blueprint**
3. Enter a name and slug
4. Add fields by clicking **Add Field**
5. Configure each field:
   - Label (display name)
   - Handle (unique identifier)
   - Type (field type)
   - Required (optional)
6. Reorder fields by dragging
7. Click **Save**

### Entries

Entries are the actual content items within a collection.

**Creating an Entry:**
1. Navigate to **Content > Entries**
2. Click **Create Entry**
3. Select a collection
4. Fill in the required fields (defined by the blueprint)
5. Set the status:
   - `draft` - Not visible publicly
   - `published` - Live and visible
   - `archived` - Hidden but preserved
6. Click **Save**

**Bulk Actions:**
1. Select multiple entries using checkboxes
2. Choose an action from the bulk action bar:
   - **Publish** - Set selected entries to published
   - **Unpublish** - Set selected entries to draft
   - **Delete** - Soft delete selected entries

**Preview Entry:**
1. Click the **Preview** button on any entry
2. View formatted content with all field values
3. Click **Edit Entry** to make changes

## User Management

Only administrators can access user management features.

**Creating a User:**
1. Navigate to **Administration > Users**
2. Click **Create User**
3. Enter user details:
   - Name
   - Email
   - Password
   - Role (Admin/Editor/Viewer)
4. Click **Save**

**Editing a User:**
- Admins can edit any user
- Users can edit their own profile
- Password is optional when updating (leave blank to keep current)

**Deleting a User:**
- Only admins can delete users
- Admins cannot delete themselves

## Activity Logging

All entry modifications are automatically tracked in the activity log.

**Logged Actions:**
- Entry creation (who, when, what)
- Entry updates (old values → new values)
- Entry deletion (preserved title and slug)

**Viewing Activity:**
Activity logs are stored in the `activity_log` table and can be queried programmatically:

```php
use App\Models\Activity;

// Get all activities for an entry
$activities = Activity::where('subject_type', Entry::class)
    ->where('subject_id', $entryId)
    ->with('causer')
    ->orderBy('created_at', 'desc')
    ->get();
```

## Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/EntriesCrudTest.php

# Run tests with coverage
php artisan test --coverage

# Run Pest directly
./vendor/bin/pest
```

### Code Quality

```bash
# Format code with Pint
./vendor/bin/pint

# Run static analysis with Larastan
./vendor/bin/phpstan analyse

# Run Rector for code improvements
./vendor/bin/rector process
```

### Database

**Resetting the database:**
```bash
php artisan migrate:fresh --seed
```

**Creating migrations:**
```bash
php artisan make:migration create_table_name
```

**Creating models:**
```bash
php artisan make:model ModelName -mfs
# -m = migration, -f = factory, -s = seeder
```

## Livewire Components

The CMS uses Livewire for interactive components:

- `Entries\Index` - Entry listing with search, filters, bulk actions
- `Entries\Create` - Entry creation form with dynamic fields
- `Entries\Edit` - Entry editing form
- `Entries\Preview` - Entry preview modal
- `Collections\Index` - Collection listing
- `Collections\Create` - Collection creation
- `Collections\Edit` - Collection editing
- `Blueprints\Index` - Blueprint listing
- `Blueprints\Create` - Blueprint creation with field builder
- `Blueprints\Edit` - Blueprint editing

## API Structure

While the CMS is primarily UI-based, the underlying architecture supports API development:

```php
// Actions for business logic
app/Livewire/Actions/
├── CreateEntry.php
├── UpdateEntry.php
└── DeleteEntry.php

// Models
app/Models/
├── Collection.php
├── Blueprint.php
├── Entry.php
├── Activity.php
└── User.php
```

## Configuration

### Authentication

Configure Fortify settings in `config/fortify.php`:
- Features (registration, password reset, two-factor authentication)
- Home path
- Authentication guard

### Database

Update `config/database.php` to change the database driver:
```php
'default' => env('DB_CONNECTION', 'sqlite'), // Change to 'mysql' or 'pgsql'
```

## Troubleshooting

### Common Issues

**Issue: "Vite manifest not found"**
```bash
npm run build
```

**Issue: "Class not found"**
```bash
composer dump-autoload
```

**Issue: "SQLSTATE connection refused"**
- Check database configuration in `.env`
- Ensure database server is running
- Run `php artisan migrate`

**Issue: Tests failing**
```bash
php artisan config:clear
php artisan cache:clear
./vendor/bin/pest
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`php artisan test`)
5. Format code (`./vendor/bin/pint`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## Testing Guidelines

- Write tests for all new features
- Follow existing test structure in `tests/Feature/`
- Use Pest syntax
- Ensure 100% test coverage for critical paths
- Run tests before committing

## License

[Add your license here]

## Credits

Built with:
- [Laravel](https://laravel.com)
- [Livewire](https://livewire.laravel.com)
- [Flux UI](https://fluxui.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [Pest PHP](https://pestphp.com)

Inspired by [Statamic CMS](https://statamic.com)

## Support

For issues and questions, please open an issue on GitHub or contact the maintainers.
