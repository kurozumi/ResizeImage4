<?php

/*
 * This file is part of ResizeImage
 *
 * Copyright(c) Akira Kurozumi <info@a-zumi.net>
 *
 * https://a-zumi.net
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ResizeImage42\Tests\DependencyInjection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Plugin\ResizeImage42\DependencyInjection\ResizeImageExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResizeImageExtensionTest extends KernelTestCase
{
    public function testIsConnectedReturnsTrueIfTableExists()
    {
        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager->method('listTableNames')->willReturn(['dtb_plugin']);

        $conn = $this->createMock(Connection::class);
        $conn->method('createSchemaManager')->willReturn($schemaManager);

        $this->assertTrue($this->invokeMethod('isConnected', [$conn]));
    }

    public function testIsConnectedReturnsFalseIfException()
    {
        $conn = $this->createMock(Connection::class);
        $conn->method('createSchemaManager')->willThrowException(new \Exception());

        $this->assertFalse($this->invokeMethod('isConnected', [$conn]));
    }

    public function testIsPluginEnabledReturnsTrue()
    {
        $stmt = $this->createMock(Result::class);
        $stmt->method('fetchOne')->willReturn(1);

        $conn = $this->createMock(Connection::class);
        $conn->method('executeQuery')->willReturn($stmt);

        $this->assertTrue($this->invokeMethod('isPluginEnabled', [$conn]));
    }

    public function testIsPluginEnabledReturnsFalse()
    {
        $stmt = $this->createMock(Result::class);
        $stmt->method('fetchOne')->willReturn(0);

        $conn = $this->createMock(Connection::class);
        $conn->method('executeQuery')->willReturn($stmt);

        $this->assertFalse($this->invokeMethod('isPluginEnabled', [$conn]));
    }

    public function testPrependDoesNotRunIfNotConnected()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $extension = $this->getMockBuilder(ResizeImageExtension::class)
            ->onlyMethods(['getConnection', 'isConnected'])
            ->getMock();

        $extension->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->createMock(Connection::class));
        $extension->expects($this->once())
            ->method('isConnected')
            ->willReturn(false);

        $container->expects($this->never())
            ->method('getExtensionConfig');

        $extension->prepend($container);
    }

    public function testPrependDoesNotRunIfPluginDisabled()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $extension = $this->getMockBuilder(ResizeImageExtension::class)
            ->onlyMethods(['getConnection', 'isConnected', 'isPluginEnabled'])
            ->getMock();

        $conn = $this->createMock(Connection::class);

        $extension->expects($this->once())
            ->method('getConnection')->willReturn($conn);
        $extension->expects($this->once())
            ->method('isConnected')->willReturn(true);
        $extension->expects($this->once())
            ->method('isPluginEnabled')->willReturn(false);

        $container->expects($this->never())
            ->method('getExtensionConfig');

        $extension->prepend($container);
    }

    protected function invokeMethod(string $methodName, array $args = [])
    {
        $object = new ResizeImageExtension();
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }

    public function testPrependModifiesLiipImagineConfig()
    {
        putenv('AWS_S3_ENABLED=true');

        $extension = $this->getMockBuilder(ResizeImageExtension::class)
            ->onlyMethods(['getConnection', 'isConnected', 'isPluginEnabled'])
            ->getMock();

        $mockCon = $this->createMock(Connection::class);
        $extension->method('getConnection')->willReturn($mockCon);
        $extension->method('isConnected')->willReturn(true);
        $extension->method('isPluginEnabled')->willReturn(true);

        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->getMock();

        $extensionConfigs = [
            'liip_imagine' => [
                [
                    'filter_sets' => [
                        'resize' => [
                            'cache' => null,
                        ],
                    ],
                ],
            ],
        ];

        $reflection = new \ReflectionProperty(ContainerBuilder::class, 'extensionConfigs');
        $reflection->setAccessible(true);
        $reflection->setValue($container, $extensionConfigs);

        $extension->prepend($container);

        $modifiedConfig = $reflection->getValue($container);
        $this->assertEquals('aws_s3_resolver', $modifiedConfig['liip_imagine'][0]['filter_sets']['resize']['cache']);
    }
}
