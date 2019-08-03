<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2019-07-23
 */

namespace Larants\Hydrate\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Larants\Hydrate\Generators\Commands\CreateEntityCommand;

class HydrateServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->commands(CreateEntityCommand::class);
    }
}