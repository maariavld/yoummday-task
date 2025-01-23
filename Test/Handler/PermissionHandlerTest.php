<?php

declare(strict_types=1);

namespace Test\Handler;

use App\Handler\PermissionHandler;
use App\Service\AuthorizationTokenFetcherService;
use App\Service\PermissionService;
use App\Service\TokenValidationService;
use App\Utils\PermissionConst;
use App\Utils\StatusCodeConst;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;
use Test\MockTokenConst;

class PermissionHandlerTest extends TestCase
{
    private TokenValidationService $tokenValidationService;
    private PermissionService $permissionService;
    private AuthorizationTokenFetcherService $authorizationTokenFetcherService;
    private ServerRequestInterface $serverRequest;
    private PermissionHandler $permissionHandler;


    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenValidationService = $this->createMock(TokenValidationService::class);
        $this->permissionService = $this->createMock(PermissionService::class);
        $this->authorizationTokenFetcherService = $this->createMock(AuthorizationTokenFetcherService::class);
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);

        $this->permissionHandler = new PermissionHandler(
            $this->tokenValidationService,
            $this->permissionService,
            $this->authorizationTokenFetcherService
        );
    }

    public function testHandleNoToken(): void
    {
        $this->authorizationTokenFetcherService
            ->method('getTokenFromHeader')
            ->willReturn(MockTokenConst::EMPTY_TOKEN);

        $response = $this->permissionHandler->__invoke($this->serverRequest, new RouteParameters(MockTokenConst::REQUEST_PARAMS));

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(StatusCodeConst::UNAUTHORIZED, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertEquals('error', $body['status']);
        $this->assertEquals('No token provided', $body['error']['message']);
    }

    public function testHandleInvalidToken(): void
    {
        $this->authorizationTokenFetcherService
            ->method('getTokenFromHeader')
            ->willReturn(MockTokenConst::INVALID_TOKEN);

        $this->tokenValidationService
            ->method('validateAndFetchToken')
            ->with(MockTokenConst::INVALID_TOKEN)
            ->willReturn(null);

        $response = $this->permissionHandler->__invoke($this->serverRequest, new RouteParameters(MockTokenConst::REQUEST_PARAMS));

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(StatusCodeConst::FORBIDDEN, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertEquals('error', $body['status']);
        $this->assertEquals('Access denied', $body['error']['message']);
    }

    public function testHandleValidTokenNoPermission(): void
    {
        $this->authorizationTokenFetcherService
            ->method('getTokenFromHeader')
            ->willReturn(MockTokenConst::VALID_TOKEN);

        $this->tokenValidationService
            ->method('validateAndFetchToken')
            ->with(MockTokenConst::VALID_TOKEN)
            ->willReturn(['permissions' => []]);

        $this->permissionService
            ->method('checkPermission')
            ->with(['permissions' => []], PermissionConst::READ)
            ->willReturn(false);

        $response = $this->permissionHandler->__invoke($this->serverRequest, new RouteParameters(MockTokenConst::REQUEST_PARAMS));

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(StatusCodeConst::FORBIDDEN, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertEquals('success', $body['status']);
        $this->assertEquals('Access denied', $body['data']['message']);
    }

    public function testHandleValidTokenWithPermission(): void
    {
        $this->authorizationTokenFetcherService
            ->method('getTokenFromHeader')
            ->willReturn(MockTokenConst::VALID_TOKEN);

        $this->tokenValidationService
            ->method('validateAndFetchToken')
            ->with(MockTokenConst::VALID_TOKEN)
            ->willReturn(['permissions' => [PermissionConst::READ]]);

        $this->permissionService
            ->method('checkPermission')
            ->with(['permissions' => [PermissionConst::READ]], PermissionConst::READ)
            ->willReturn(true);

        $response = $this->permissionHandler->__invoke($this->serverRequest, new RouteParameters(MockTokenConst::REQUEST_PARAMS));

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(StatusCodeConst::OK, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertEquals('success', $body['status']);
        $this->assertEquals('Access granted', $body['data']['message']);
    }
}
