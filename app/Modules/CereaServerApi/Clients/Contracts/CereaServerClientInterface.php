<?php

namespace App\Modules\CereaServerApi\Clients\Contracts;

interface CereaServerClientInterface extends \App\Foundation\Clients\Contracts\ClientInterface
{
    public function getCereaVersions(): ?array;
}
