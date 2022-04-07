<?php namespace App\Foundation;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->resolving(Clients\Contracts\ClientInterface::class, function (Clients\Contracts\ClientInterface $client)
        {
            $client->setGuzzle(app()->make(\GuzzleHttp\Client::class));
        });
    }
}
