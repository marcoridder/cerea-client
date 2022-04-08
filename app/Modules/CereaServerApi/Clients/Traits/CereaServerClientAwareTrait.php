<?php namespace App\Modules\CereaServerApi\Clients\Traits;

use App\Modules\CereaServerApi\Clients\Contracts\CereaServerClientInterface;

trait CereaServerClientAwareTrait
{
    /** @var CereaServerClientInterface */
    protected $cereaServerClient;

    public function setCereaServerClient(CereaServerClientInterface $cereaServerClient): void
    {
        $this->cereaServerClient = $cereaServerClient;
    }
}
