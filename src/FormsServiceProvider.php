<?php

namespace Reno\Forms;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Reno\Cms\Events\AdminApiRoutesRegistering;
use Reno\Cms\Events\DashboardBlocksCollecting;
use Reno\Cms\Events\JsTranslationFilesRegistering;
use Reno\Cms\Events\JavascriptRoutesRegistering;
use Reno\Cms\Events\PermissionsRegistering;
use Reno\Cms\Events\TopMenuItemsRegistering;
use Reno\Forms\Dashboard\RecentSubmissionsBlock;
use Reno\Forms\Http\Controllers\Admin\ConsentAcceptanceController;
use Reno\Forms\Http\Controllers\Admin\FormSubmissionController;
use Reno\Forms\Plugins\Menu\ConsentAcceptancesMenuItem;
use Reno\Forms\Plugins\Menu\FormsMenuContainer;
use Reno\Forms\Plugins\Menu\FormSubmissionsMenuItem;
use Reno\Forms\Plugins\Routes\ConsentAcceptancesRoute;
use Reno\Forms\Plugins\Routes\FormSubmissionShowRoute;
use Reno\Forms\Plugins\Routes\FormSubmissionsRoute;

class FormsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/forms.php', 'forms');
        $this->mergeConfigFrom(__DIR__ . '/../config/forms-bindings.php', 'forms-bindings');
        $this->registerBindings();

        $this->registerAdminApiRoutes();
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'forms');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'forms');

        $this->publishes([
            __DIR__ . '/../config/forms.php' => config_path('forms.php'),
        ], 'cms-config');
        $this->publishes([
            __DIR__ . '/../config/forms-bindings.php' => config_path('forms-bindings.php'),
        ], 'cms-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'forms-migrations');

        $this->publishes([
            __DIR__ . '/../public/build' => public_path('js/reno/forms/build'),
        ], 'cms-assets');

        $this->registerMenuItems();
        $this->registerJavascriptRoutes();
        $this->registerPermissions();
        $this->registerJsTranslations();
        $this->registerDashboardBlocks();
    }

    private function registerMenuItems(): void
    {
        Event::listen(TopMenuItemsRegistering::class, function (TopMenuItemsRegistering $event): void {
            $event->add(new FormsMenuContainer());
            $event->add(new FormSubmissionsMenuItem());
            $event->add(new ConsentAcceptancesMenuItem());
        });
    }

    private function registerJavascriptRoutes(): void
    {
        Event::listen(JavascriptRoutesRegistering::class, function (JavascriptRoutesRegistering $event): void {
            $event->add(new FormSubmissionsRoute());
            $event->add(new FormSubmissionShowRoute());
            $event->add(new ConsentAcceptancesRoute());
        });
    }

    private function registerAdminApiRoutes(): void
    {
        Event::listen(AdminApiRoutesRegistering::class, function (): void {
            Route::middleware('cms.permission:forms.submissions.view')
                ->prefix('/forms/submissions')
                ->group(function (): void {
                    Route::get('/', [FormSubmissionController::class, 'index']);
                    Route::get('/{id}', [FormSubmissionController::class, 'show'])->whereNumber('id');
                });

            Route::middleware('cms.permission:forms.consents.view')
                ->prefix('/forms/consents')
                ->group(function (): void {
                    Route::get('/', [ConsentAcceptanceController::class, 'index']);
                    Route::delete('/{id}', [ConsentAcceptanceController::class, 'destroy'])->whereNumber('id');
                });
        });
    }

    private function registerPermissions(): void
    {
        Event::listen(PermissionsRegistering::class, function (PermissionsRegistering $event): void {
            $event->addPermission('forms.submissions.view', 'forms');
            $event->addPermission('forms.consents.view', 'forms');
        });
    }

    private function registerJsTranslations(): void
    {
        Event::listen(JsTranslationFilesRegistering::class, function (JsTranslationFilesRegistering $event): void {
            $event->addFile(__DIR__ . '/../resources/lang/' . $event->getLocale() . '/forms.php');
        });
    }

    private function registerDashboardBlocks(): void
    {
        Event::listen(DashboardBlocksCollecting::class, function (DashboardBlocksCollecting $event): void {
            $user = auth()->user();
            if (!$user || !$user->can('forms.submissions.view')) {
                //return; // TODO
            }

            $event->addBlock(new RecentSubmissionsBlock());
        });
    }

    private function registerBindings(): void
    {
        $bindings = (array) config('forms-bindings.bindings', []);
        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }

        $singletons = (array) config('forms-bindings.singletons', []);
        foreach ($singletons as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }
    }
}
