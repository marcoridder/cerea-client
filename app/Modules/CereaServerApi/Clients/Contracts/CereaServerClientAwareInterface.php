<?php namespace App\Modules\CereaServerApi\Clients\Contracts;

interface CereaServerClientAwareInterface
{
    public function setCereaServerClient(CereaServerClientInterface $cereaServerClient): void;
}
