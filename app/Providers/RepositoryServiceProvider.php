<?php

namespace App\Providers;

use App\Repositories\Feature\Mongodb\FeatureRepository;
use App\Repositories\Item\Mongodb\ItemRepository;
use App\RepositoryInterface\Feature\FeatureRepositoryInterface;
use App\RepositoryInterface\Item\ItemRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
        $this->app->bind(FeatureRepositoryInterface::class, FeatureRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
