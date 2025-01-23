<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\AuthorizationTokenFetcherService;
use App\Service\PermissionService;
use App\Service\TokenValidationService;
use App\Utils\PermissionConst;
use App\Utils\StatusCodeConst;
use App\Utils\TokenConst;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;

#[Route(httpMethod: HttpMethod::GET, uri: '/has_permission')]
class PermissionHandler implements HandlerInterface
{
    /**
     * Dependency Injection would be available here
     */
    public function __construct(
        protected TokenValidationService $tokenValidationService,
        protected PermissionService $permissionService,
        protected AuthorizationTokenFetcherService $authorizationTokenFetcherService,
    ) {
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $requestToken = $this->authorizationTokenFetcherService->getTokenFromHeader($serverRequest);

        if($requestToken === TokenConst::NO_TOKEN) {
            return new JSONResponse([
                'status' => 'error',
                'error' => [
                    'code' => StatusCodeConst::UNAUTHORIZED,
                    'message' => 'No token provided',
                    'details' => 'Please provide a valid Bearer token in the Authorization header.'
                ]
            ], StatusCodeConst::UNAUTHORIZED);
        }

        $tokenData = $this->tokenValidationService->validateAndFetchToken($requestToken);
        if (!$tokenData) {
            return new JSONResponse([
                'status' => 'error',
                'error' => [
                    'code' => StatusCodeConst::FORBIDDEN,
                    'message' => 'Access denied'
                ]
            ], StatusCodeConst::FORBIDDEN);
        }

        $hasPermission = $this->permissionService->checkPermission($tokenData, PermissionConst::READ);

        return new JSONResponse(
            [
                'status' => 'success',
                'data' => [
                    'permission' => $hasPermission,
                    'message' => $hasPermission ? 'Access granted' : 'Access denied'
                ]
            ],
            $hasPermission ? StatusCodeConst::OK : StatusCodeConst::FORBIDDEN
        );
    }
}
