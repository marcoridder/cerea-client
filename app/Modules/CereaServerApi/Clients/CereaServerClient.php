<?php namespace App\Modules\CereaServerApi\Clients;

use App\Foundation\Clients\AbstractClient;
use App\Foundation\Clients\ApiException;

class CereaServerClient extends AbstractClient implements Contracts\CereaServerClientInterface
{
    public static $endpoint = '/';

    public function __construct()
    {
    }

    public function getCereaVersions(): ?array
    {
        try {
            return $this->call("cerea-versions");
        } catch (ApiException $exception) {

            if($exception->getCode() === 404) {
               // return null;
            }
            throw $exception;
        }
    }

}
