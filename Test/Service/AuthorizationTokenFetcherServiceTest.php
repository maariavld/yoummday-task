<?php

declare(strict_types=1);

namespace Test\Service;

use App\Service\AuthorizationTokenFetcherService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Test\MockTokenConst;

class AuthorizationTokenFetcherServiceTest extends TestCase
{
    private AuthorizationTokenFetcherService $authorizationTokenFetcherService;

    protected function setUp(): void
    {
        $this->authorizationTokenFetcherService = new AuthorizationTokenFetcherService();
    }

    /**
     * @throws Exception
     */
    public function testGetTokenFromHeaderWithValidToken(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest
            ->method('getHeaderLine')
            ->with('Authorization')
            ->willReturn(sprintf('Bearer %s', MockTokenConst::VALID_TOKEN));

        $result = $this->authorizationTokenFetcherService->getTokenFromHeader($serverRequest);

        $this->assertEquals(MockTokenConst::VALID_TOKEN, $result);
    }

    /**
     * @throws Exception
     */
    public function testGetTokenFromHeaderWithNoToken(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest
            ->method('getHeaderLine')
            ->with('Authorization')
            ->willReturn(MockTokenConst::EMPTY_TOKEN);

        $result = $this->authorizationTokenFetcherService->getTokenFromHeader($serverRequest);

        $this->assertEquals(MockTokenConst::EMPTY_TOKEN, $result);
    }
}