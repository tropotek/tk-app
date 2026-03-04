<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

# copy assets to public folder
//        cp -f vendor/twbs/bootstrap/dist/css/bootstrap.min.css* public/css/
//        cp -f vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js* public/js/
//        cp -f vendor/htmx/htmx/htmx.min.js public/js/
//        cp -f vendor/jquery/jquery/jquery-3.7.1.min.js public/js/jquery.min.js
//
//        # database setup
//        if [[ ! -f database/storage/database.sqlite ]]; then
//          echo "  Generating APP_KEY"
//          php artisan key:generate --force
//          echo "  Creating Database"
//          touch database/storage/database.sqlite
//          echo "  Migrating Database"
//          php artisan migrate:fresh --seed
//
//          php artisan storage:link
//        fi
//
//        cd ..
//        chown -r 1000:1000 ./


    }
}
