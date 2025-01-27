<?php

namespace App\Service;

use App\Logger\MonologLogger;
use App\Provider\TokenDataProvider;

class TokenValidationService extends MonologLogger
{
    public function __construct(protected TokenDataProvider $tokenDataProvider) {}

    public function validateAndFetchToken(string $requestToken): ?array
    {
        $logger = $this->getLogger();
        $logger->info("Validating token");

        if (!$this->isValidToken($requestToken)) {
            $logger->error("Invalid token");
            return null;
        }

        $tokens = $this->tokenDataProvider->getTokens();
        foreach ($tokens as $t) {
            if ($t["token"] === $requestToken) {
                $logger->info("Token found");
                return $t;
            }
        }

        return null;
    }

    private function isValidToken(string $token): bool
    {
        return !empty($token);
    }
}