<?php

namespace Hotrush\Crawler\Client;

use GuzzleHttp\Psr7\Response;
use Hotrush\Crawler\UserAgent\UserAgentInterface;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class PuppeteerClient implements ClientInterface
{
    /**
     * @var UserAgentInterface
     */
    private $userAgent;

    /**
     * @var Puppeteer
     */
    private $puppeteer;

    private $browserOptions = [];

    private $browser;

    private $cookies;

    /**
     * PuppeteerClient constructor.
     *
     * @param UserAgentInterface $userAgent
     */
    public function __construct(UserAgentInterface $userAgent)
    {
        $this->userAgent = $userAgent;

        $this->puppeteer = new Puppeteer([
            'read_timeout' => 30,
        ]);

        $this->browserOptions = [
            'headless' => true,
            'ignoreHTTPSErrors' => true,
            'args' => [
                '--no-sandbox',
                '--user-agent="'.$this->userAgent->getUserAgent().'"'
            ]
        ];
        $this->browser = $this->puppeteer->launch($this->browserOptions);
    }

    /**
     * @param $method
     * @param UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function request($method, UriInterface $uri, array $options = []): ResponseInterface
    {
        $page = $this->browser->newPage();

        if ($this->cookies) {
            echo json_encode($this->cookies).PHP_EOL;
            $page->setCookie(...$this->cookies);
        }

        if ($method === 'POST' && isset($options['form_params'])) {
            $page->setRequestInterception(true);
            $page->on('request', new JsFunction(
                ['interceptedRequest'],
                "
                var headers = interceptedRequest.headers();
                headers['Content-Type'] = 'application/x-www-form-urlencoded';
                if (".json_encode($options['headers'] ?? []).".length > 0) {
                    Object.assign(headers, ".json_encode($options['headers'] ?? []).");
                }
                var data = {
                    'method': 'POST',
                    'postData': '".http_build_query($options['form_params'])."',
                    'headers': headers
                };
                interceptedRequest.continue(data);
                "
            ));
        } else if ($method === 'GET') {
            $page->setRequestInterception(true);
            $page->on('request', new JsFunction(
                ['interceptedRequest'],
                "
                if (['image', 'stylesheet', 'font', 'script'].indexOf(interceptedRequest.resourceType()) !== -1) {
                    interceptedRequest.abort();
                } else {
                    interceptedRequest.continue();
                }
                "
            ));
        }

        $response = $page->goto((string) $uri, [
            'timeout' => 30000,
        ]);

        if (!$response || !$page->content()) {
            throw new \RuntimeException("Puppeteer can not get page content for {$uri}");
        }

        $headers = $response->headers();
        $content = $page->content();
        $this->cookies = $page->cookies();
        $page->close();

        return new Response(
            $headers['status'] ?? 200,
            $headers,
            $content
        );
    }

    /**
     * Close the browser on destruction.
     */
    public function __destruct()
    {
        $this->browser->close();
    }
}
