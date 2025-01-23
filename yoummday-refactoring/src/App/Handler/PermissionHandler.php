<?php

declare(strict_types=1);

namespace App\Handler;

use App\Provider\TokenDataProvider;
use App\Utils\PermissionConst;
use App\Utils\StatusCodeConst;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(httpMethod: HttpMethod::GET, uri: '/has_permission/{token}')]
class PermissionHandler implements HandlerInterface
{
    private Logger $logger;
    /**
     * Dependency Injection would be available here
     */
    public function __construct(
        protected TokenDataProvider $tokenDataProvider,
    ) {
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $this->setLogger(new Logger('app'));

        $requestToken = $parameters->get("token");
        $this->logger->info("Checking permission for token: $requestToken");

        $tokenData = $this->findToken($requestToken);

        if (!$tokenData) {
            return new JSONResponse(['permission' => false], StatusCodeConst::FORBIDDEN);
        }

        $hasPermission = in_array(PermissionConst::READ, $tokenData['permissions'], true);

        return new JSONResponse(
            ['permission' => $hasPermission],
            $hasPermission ? StatusCodeConst::OK : StatusCodeConst::FORBIDDEN
        );
    }

    private function findToken($requestToken): ?array
    {
        $tokens = $this->tokenDataProvider->getTokens();
        foreach ($tokens as $t) {
            if ($t["token"] == $requestToken) {
                return $t;
            }
        }

        return null;
    }

    #[Required]
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
        $this->logger->pushHandler(new StreamHandler('php://stdout', Level::Debug));
    }

}
