<?php namespace App\Foundation\Clients;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractClient implements Contracts\ClientInterface
{
    static $endpoint;

    /** @var string */
    protected $authHeaderName = "Authorization";

    /** @var string */
    protected $token;

    /** @var string */
    protected $baseUrl;

    /** @var ?string */
    protected $version;

    /** @var Guzzle */
    private $guzzle;

    private $url;

    private $method;

    /** @var ResponseInterface|null */
    private $response;

    /** @var array */
    private $options;

    /** @var float */
    private $startTime;

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    public function setGuzzle(\GuzzleHttp\ClientInterface $guzzle): void
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @return string
     */
    public function getAuthHeaderName(): string
    {
        return $this->authHeaderName;
    }

    /**
     * @param string $authHeaderName
     */
    public function setAuthHeaderName(string $authHeaderName): void
    {
        $this->authHeaderName = $authHeaderName;
    }

    /**
     * @param string $uri
     * @param array  $options
     * @param string $method
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    protected function call(string $uri, array $options = [], string $method = "GET"): ?array
    {
        $this->method = $method;
        $this->url = $this->_buildUrl($uri);
        $this->options = $options;
        try {
            $this->startTime = microtime(true);
            $options = array_merge_recursive([
                'headers'         => [
                    $this->authHeaderName => $this->token,
                    'accept'          => 'application/json',
                ],
                'autoreferer'     => true,
                'follow_location' => true,
                'timeout'         => 10,
                'verify'          => false,
                'stream'          => false,
            ], $options);
            $this->response = $this->guzzle->request($method, $this->_buildUrl($uri), $options);
        } catch (GuzzleException $e) {
            report($e);
            $this->response = $e->getResponse();
            $this->_log();
            if ( $this->response ) {
                throw new ApiException($this->response->getBody()->__toString(), $e->getCode(), $e);
            }
            throw $e;
        }
        $this->_log();

        return json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    private function _buildUrl(string $uri): string
    {
        $parts = [$this->baseUrl, $this->version, $this::$endpoint, $uri];
        $parts = array_map(function ($part)
        {
            return trim($part, '/');
        }, $parts);
        $parts = array_filter($parts);

        return implode('/', $parts);
    }

    private function _log(): void
    {

        if ( $this->response ) {

            $json = json_decode($this->response->getBody()->__toString(), true);
            if ( $this->response->getStatusCode() >= 400 ) {
                logger()->error($this->formatMessage(), ['options' => $this->options, 'response' => $json]);
            } elseif ( ! app()->environment('production') ) {
                logger()->debug($this->formatMessage(), ['options' => $this->options, 'response' => $json]);
            }
        }
    }

    private function formatMessage(): string
    {
        $duration = microtime(true) - $this->startTime;

        return "'" . number_format($duration, 2) . "s | {$this->method} {$this->url}'";
    }
}
