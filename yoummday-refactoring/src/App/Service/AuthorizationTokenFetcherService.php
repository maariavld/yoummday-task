<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\TokenConst;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationTokenFetcherService
{
    public function getTokenFromHeader(ServerRequestInterface $serverRequest): string
    {
        $authorizationHeader = $serverRequest->getHeaderLine('Authorization');

        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return $matches[1]; // Return the token part of the Authorization header
        }

        return TokenConst::NO_TOKEN; // Return a constant if no token is provided
    }
}
