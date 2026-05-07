<?php

declare(strict_types=1);

namespace RyanHellyer\UniqueHeaders\Tests;

use PHPUnit\Framework\TestCase;
use RyanHellyer\UniqueHeaders\Vendor\Psr\Container\ContainerInterface;
use RyanHellyer\UniqueHeaders\AdminModule;
use RyanHellyer\UniqueHeaders\AttachmentHelper;
use RyanHellyer\UniqueHeaders\DisplayModule;

class SmokeTest extends TestCase
{
    public function testAdminModuleId(): void
    {
        $module = new AdminModule(new AttachmentHelper());

        $this->assertSame('unique-headers-admin', $module->id());
    }

    public function testAdminModuleRunReturnsTrue(): void
    {
        $module = new AdminModule(new AttachmentHelper());
        $container = $this->createStub(ContainerInterface::class);

        $this->assertTrue($module->run($container));
    }

    public function testDisplayModuleId(): void
    {
        $module = new DisplayModule(new AttachmentHelper());

        $this->assertSame('unique-headers-display', $module->id());
    }
}
