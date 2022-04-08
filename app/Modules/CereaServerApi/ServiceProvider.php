<?php namespace App\Modules\CereaServerApi;

use App\Foundation\Traits\MakesAware;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    use MakesAware;

    public function register()
    {
        $this->app->resolving(Clients\Contracts\CereaServerClientInterface::class, function (Clients\Contracts\CereaServerClientInterface $class)
        {
            $class->setBaseUrl(config('cereaserver.baseUrl'));
            $class->setVersion(config('cereaserver.version'));
            $class->setToken('Bearer ' . config('appconfig.cereaserver_token'));
            $class->setAuthHeaderName(config('cereaserver.authHeaderName'));
        });
        $this->_registerClients();
        $this->_makeClientsAware();
    }

    private function _registerClients()
    {
        app()->singleton(Clients\Contracts\CereaServerClientInterface::class, Clients\CereaServerClient::class);
    }

    private function _makeClientsAware()
    {
        $this->makeAware(Clients\Contracts\CereaServerClientAwareInterface::class);
    }

}
