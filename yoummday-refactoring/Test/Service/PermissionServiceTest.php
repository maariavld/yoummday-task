<?php

declare(strict_types=1);

namespace Test\Service;

use App\Service\PermissionService;
use App\Utils\PermissionConst;
use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    private PermissionService $permissionService;

    protected function setUp(): void
    {
        $this->permissionService = new PermissionService();
    }

    public function testCheckPermissionWithValidPermission(): void
    {
        $tokenData = ['permissions' => [PermissionConst::READ]];

        $result = $this->permissionService->checkPermission($tokenData, PermissionConst::READ);

        $this->assertTrue($result);
    }

    public function testCheckPermissionWithInvalidPermission(): void
    {
        $tokenData = ['permissions' => [PermissionConst::WRITE]];

        $result = $this->permissionService->checkPermission($tokenData, PermissionConst::READ);

        $this->assertFalse($result);
    }
}
