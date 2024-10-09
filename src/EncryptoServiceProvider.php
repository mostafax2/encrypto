<?php

namespace Mostafax\Encrypto;

use Illuminate\Support\ServiceProvider;

class EncryptoServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind any services here
        include __DIR__.'/routes/web.php';
    }

    public function boot()
    {
        // Load routes, views, migrations, etc.
    }
}
