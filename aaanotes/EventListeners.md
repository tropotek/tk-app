Here’s the minimal Laravel setup so `php artisan event:list` can show your file events.

## 1) Create listeners
You need at least one listener per event if you want it to appear in the list.

Example listeners:

- `Tk\Listeners\LogFileUploaded`
- `Tk\Listeners\LogFileDeleted`

## 2) Register them in an Event Service Provider
If your app doesn’t already have one, create `app/Providers/EventServiceProvider.php` and register the event/listener mapping.

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Tk\Events\FileDeletedEvent;
use Tk\Events\FileUploadedEvent;
use Tk\Listeners\LogFileDeleted;
use Tk\Listeners\LogFileUploaded;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        FileUploadedEvent::class => [
            LogFileUploaded::class,
        ],
        FileDeletedEvent::class => [
            LogFileDeleted::class,
        ],
    ];
}
```


## 3) Register the provider
Your app currently only loads `AppServiceProvider`, so Laravel won’t know about the event provider unless you add it.

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
];
```


## 4) Create the listeners
For example:

```php
<?php

namespace App\Listeners;

use Tk\Events\FileUploadedEvent;

class LogFileUploaded
{
    public function handle(FileUploadedEvent $event): void
    {
        logger()->info('File uploaded listener ran', [
            'file' => $event->file,
        ]);
    }
}
```


```php
<?php

namespace App\Listeners;

use Tk\Events\FileDeletedEvent;

class LogFileDeleted
{
    public function handle(FileDeletedEvent $event): void
    {
        logger()->info('File deleted listener ran', [
            'file' => $event->file,
        ]);
    }
}
```


## 5) Clear cached manifest
If needed, refresh Laravel’s cached event/provider data:

```shell script
php artisan optimize:clear
```


## After that
`php artisan event:list` should show your events with their listeners.

## Small note
Your event constructors currently log immediately when the event object is instantiated. That’s fine for debugging, but it’s separate from listener registration.

If you want, I can also show you how to make this work **inside the package** instead of the app, so the package registers its own events automatically.
