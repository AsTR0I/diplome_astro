<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use App\Observers\VboxDialoutObserver;
use App\Observers\PhonebookObserver;
use App\Observers\VboxCoreObserver;
use App\Observers\AccountObserver;
use App\Observers\SippeerObserver;
use App\Observers\RouteObserver;
use App\Observers\PeerObserver;
use App\Observers\DidObserver;
use App\VboxDialout;
use App\Phonebook;
use App\VboxCore;
use App\Account;
use App\Sippeer;
use App\Route;
use App\Peer;
use App\Did;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Используется для формировани яответа в контроллерах
         * Пример: return response()->success('foo');
         */
        Response::macro('success', function ($message, $payload = null) {
            $type = 'success';
            return Response::make(compact('type', 'message', 'payload'));
        });

        /**
         * Используется для формировани яответа в контроллерах
         * Пример: return response()->danger('foo');
         */
        Response::macro('danger', function ($message, $payload = null) {
            $type = 'danger';
            return Response::make(compact('type', 'message', 'payload'));
        });

    }
}
