<?php

declare(strict_types=1);

namespace App\Handler;

use App\Logger\MonologLogger;
use App\Provider\TokenDataProvider;
use App\Service\TokenValidationService;
use App\Utils\PermissionConst;
use App\Utils\StatusCodeConst;
use App\Utils\TokenConst;
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

#[Route(httpMethod: HttpMethod::GET, uri: '/has_permission')]
class PermissionHandler implements HandlerInterface
{
    /**
     * Dependency Injection would be available here
     */
    public function __construct(
        protected TokenValidationService $tokenValidationService,
    ) {
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $requestToken = $this->getTokenFromHeader($serverRequest);

        if($requestToken === TokenConst::NO_TOKEN) {
            return new JSONResponse(['permission' => false], StatusCodeConst::UNAUTHORIZED);
        }

        $tokenData = $this->tokenValidationService->validateAndFetchToken($requestToken);
        if (!$tokenData) {
            return new JSONResponse(['permission' => false], StatusCodeConst::FORBIDDEN);
        }

        $hasPermission = $this->checkPermission($tokenData, PermissionConst::READ);

        return new JSONResponse(
            ['permission' => $hasPermission],
            $hasPermission ? StatusCodeConst::OK : StatusCodeConst::FORBIDDEN
        );
    }

    private function checkPermission(array $tokenData, string $permissionLevel): bool
    {
        return in_array($permissionLevel, $tokenData['permissions'], true);
    }

    private function getTokenFromHeader(ServerRequestInterface $serverRequest): string
    {
        $authorizationHeader = $serverRequest->getHeaderLine('Authorization');

        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return $matches[1];
        }

        return TokenConst::NO_TOKEN;
    }
}
