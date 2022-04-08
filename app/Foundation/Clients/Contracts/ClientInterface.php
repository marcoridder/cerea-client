<?php namespace App\Foundation\Clients\Contracts;

interface ClientInterface
{
    public function setGuzzle(\GuzzleHttp\ClientInterface $guzzle): void;

    public function setToken(string $token): void;

    public function setVersion(?string $version): void;

    public function setBaseUrl(string $baseUrl): void;

    public function getAuthHeaderName(): string;

    public function setAuthHeaderName(string $authHeaderName): void;
}
