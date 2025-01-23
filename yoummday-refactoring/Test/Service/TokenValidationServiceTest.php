<?php

declare(strict_types=1);

namespace Test\Service;

use App\Service\TokenValidationService;
use App\Provider\TokenDataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Test\MockTokenConst;

class TokenValidationServiceTest extends TestCase
{
    private TokenValidationService $tokenValidationService;
    private TokenDataProvider $tokenDataProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenDataProvider = $this->createMock(TokenDataProvider::class);
        $this->tokenValidationService = new TokenValidationService($this->tokenDataProvider);
    }

    public function testValidateAndFetchTokenWithValidToken(): void
    {
        $tokenData = ['token' =>MockTokenConst::VALID_TOKEN, 'permissions' => ['read']];

        $this->tokenDataProvider
        ->method('getTokens')
        ->willReturn([$tokenData]);

        $result = $this->tokenValidationService->validateAndFetchToken(MockTokenConst::VALID_TOKEN);

        $this->assertEquals($tokenData, $result);
    }

    public function testValidateAndFetchTokenWithInvalidToken(): void
    {
        $this->tokenDataProvider
        ->method('getTokens')
        ->willReturn([['token' => MockTokenConst::VALID_TOKEN, 'permissions' => ['read']]]);

        $result = $this->tokenValidationService->validateAndFetchToken(MockTokenConst::INVALID_TOKEN);

        $this->assertNull($result);
    }

    public function testValidateAndFetchTokenWithEmptyToken(): void
    {
        $result = $this->tokenValidationService->validateAndFetchToken(MockTokenConst::EMPTY_TOKEN);

        $this->assertNull($result);
    }
}
