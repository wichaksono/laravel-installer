# NeonWebID Module

A Laravel module for web installation and activity logging.

## Installation Steps

### Step 1: Upload Module
Copy or upload the `NeonWebId` folder to your Laravel project:

```bash
# If Modules directory doesn't exist, create it first
mkdir -p Modules

# Copy NeonWebId module to Modules directory
cp -r NeonWebId Modules/

# Your structure should look like this:
your-laravel-project/
├── app/
├── config/
├── database/
├── Modules/           # <- This directory
│   └── NeonWebId/    # <- Module here
└── ...
```

### Step 2: Register Module
Add the following to your `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "Modules\\": "Modules/"
        }
    }
}
```

Then run:
```bash
composer dump-autoload
```

## Usage Guide

### 1. Basic Activity Logging

```php
use Modules\NeonWebId\Models\ActivityLog;

// Simple logging
$post = Post::create(['title' => 'Hello World']);

ActivityLog::log(
    'posts',                // Category
    'Created new post',     // What happened
    $post,                  // What was affected
    auth()->user(),        // Who did it
    [
        'title' => $post->title,
        'ip' => request()->ip()
    ]
);
```

### 2. Using With Models
Add the trait to your model:

```php
use Modules\NeonWebId\Traits\HasActivityLogs;

class Post extends Model
{
    use HasActivityLogs;

    // Auto log when post is created
    protected static function booted()
    {
        static::created(function ($post) {
            $post->logActivity(
                'posts', 
                'Post was created'
            );
        });

        // Auto log when post is updated
        static::updated(function ($post) {
            $post->logActivity(
                'posts',
                'Post was updated',
                ['changes' => $post->getDirty()]
            );
        });
    }
}
```

### 3. Retrieving Logs

```php
// Get all logs
$logs = ActivityLog::all();

// Get logs with relationships
$logs = ActivityLog::with(['subject', 'causer'])->get();

// Get logs for specific post
$post = Post::find(1);
$postLogs = $post->activities;

// Get logs by user
$userLogs = ActivityLog::where('causer_id', auth()->id())
    ->where('causer_type', User::class)
    ->latest()
    ->get();

// Format log output
foreach ($logs as $log) {
    echo sprintf(
        '%s %s %s at %s',
        $log->causer->name,          // Who
        $log->description,           // Did what
        $log->subject->title,        // To what
        $log->created_at->format('Y-m-d H:i:s')
    );
}
```

### 4. Common Logging Patterns

```php
// User Activities
ActivityLog::log(
    'users',
    'Logged in',
    auth()->user(),
    auth()->user(),
    ['ip' => request()->ip()]
);

// Content Changes
ActivityLog::log(
    'posts',
    'Updated post content',
    $post,
    auth()->user(),
    [
        'old' => $post->getOriginal('content'),
        'new' => $post->content
    ]
);

// System Events
ActivityLog::log(
    'system',
    'Backup completed',
    null,
    null,
    ['size' => '2.5GB']
);
```

### 5. Advanced Querying

```php
// Get today's logs
$today = ActivityLog::whereDate('created_at', today())->get();

// Get logs by category
$userLogs = ActivityLog::where('log_name', 'users')->get();

// Get logs with specific properties
$logs = ActivityLog::whereJsonContains('properties->status', 'approved')->get();

// Get latest logs for each subject
$latestLogs = ActivityLog::latest()
    ->groupBy('subject_type', 'subject_id')
    ->get();
```

**Note:** Make sure to clear cache after installation:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

# Git Auto Pull Feature

## Configuration

1. Add configuration to `config/services.php`:
```php
'git' => [
    'auto_pull' => [
        'enabled' => env('GIT_AUTO_PULL_ENABLED', false),
        'frequency' => env('GIT_AUTO_PULL_FREQUENCY', 'hourly'),
        'cron' => env('GIT_AUTO_PULL_CRON', '0 * * * *'),
    ],
    'credentials' => [
        'username' => env('GIT_USERNAME'),
        'token' => env('GIT_TOKEN'),
    ],
],
```

2. Configure repositories in `config/neonwebid/git.php`:
```php
'repositories' => [
    'my-repo' => [
        'path' => base_path(),  // or any other repository path
        'branch' => 'main',     // optional
        'credentials' => [      // optional for private repos
            'username' => config('services.git.credentials.username'),
            'token' => config('services.git.credentials.token')
        ]
    ]
]
```

3. Add to `.env`:
```env
GIT_AUTO_PULL_ENABLED=true
GIT_AUTO_PULL_FREQUENCY=hourly  # hourly, daily, or custom
GIT_AUTO_PULL_CRON="0 * * * *" # if frequency is custom
GIT_USERNAME=your-username      # for private repos
GIT_TOKEN=your-token           # for private repos
```

## Important Note
After changing configuration, remember to:
```bash
php artisan config:cache
```

## Created By
**Author:** wichaksono  
**Created:** 2025-05-17 07:37:24

## License
MIT License
