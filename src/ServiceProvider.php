<?php

namespace Impres\Translations;

use Statamic\Providers\AddonServiceProvider;
use Statamic\CP\Navigation\Nav;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Statamic;
use Illuminate\Support\Facades\Route;
use Impres\Translations\Http\Controllers\TranslationController;

class ServiceProvider extends AddonServiceProvider
{

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    public function bootAddon()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'impres-translations');

        Statamic::pushCpRoutes(fn () => Route::name('translation-manager.')->prefix('translations')->group(function () {
            Route::get('/', [TranslationController::class, 'index'])->name('index');
            Route::get('/import', [TranslationController::class, 'getImport'])->name('import');
            Route::get('/locale/{locale}/edit', [TranslationController::class, 'edit'])->name('import-locale');
            Route::get('/export', [TranslationController::class, 'getExport'])->name('export');
            Route::get('locale/{locale}/update', [TranslationController::class, 'update'])->name('export-locale');
        }));
//
//        NavAPI::extend(fn (Nav $nav) => $nav
//            ->content(__('Translations'))
//            ->section(__('Tools'))
//            ->route('translation-manager.index')
//            ->icon('content-writing')
//        );
//
//        Nav::extend(function ($nav) {
//            $nav->content('Translations')
//                ->route('translation-manager.index')
//                ->icon('globe');
//        });
//
//        Nav::extend(function ($nav) {
//            $nav->create('Import')
//                ->section('Translations')
//                ->route('translation-manager.import')
//                ->icon('globe');
//        });
//        Nav::extend(function ($nav) {
//            $nav->create('Translations')
//                ->section('Export')
//                ->route('translation-manager.export')
//                ->icon('globe');
//        });
        $this->bootNav();
    }

    private function bootNav()
    {
        NavAPI::extend(fn (Nav $nav) => $nav
            ->content(__('Translations'))
            ->section(__('Tools'))
            ->route('translation-manager.index')
            ->icon('content-writing')
        );

        NavAPI::extend(fn (Nav $nav) => $nav
            ->content(__('Translations'))
            ->section(__('Translations'))
            ->route('translation-manager.export')
            ->icon('content-writing')
        );


    }
}
