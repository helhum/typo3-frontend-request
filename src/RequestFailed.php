<?php
declare(strict_types=1);
namespace Helhum\Typo3FrontendRequest;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestFailed extends \Exception
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(string $message, RequestInterface $request, ResponseInterface $response, \Throwable $previous = null)
    {
        parent::__construct($message, 1552060849, $previous);
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
