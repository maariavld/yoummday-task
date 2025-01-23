<?php

declare(strict_types=1);

namespace App\Service;

class PermissionService
{
    public function checkPermission(array $tokenData, string $permissionLevel): bool
    {
        return in_array($permissionLevel, $tokenData['permissions'], true);
    }
}
