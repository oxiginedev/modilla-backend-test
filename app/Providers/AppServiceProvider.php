<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModels();
        $this->configureCommands();
        $this->configureDates();

        // ResourceCollection::withoutWrapping();

        // Gives admin users super powers :)
        Gate::before(function (User $user, $ability) {
            if ($user->hasRole(UserRole::ADMIN)) {
                return true;
            }
        });
    }

    protected function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
        Model::unguard();
    }

    protected function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    protected function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
