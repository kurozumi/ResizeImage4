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
use Plugin\ResizeImage42\DependencyInjection\ResizeImageExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResizeImageExtensionTest extends KernelTestCase
{
    private ResizeImageExtension $extension;

    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new ResizeImageExtension();
    }

    public function testPrependSkipsWhenNotConnected()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $extension = $this->getMockBuilder(ResizeImageExtension::class)
            ->onlyMethods(['getConnection', 'isConnected'])
            ->getMock();

        $mockCon = $this->createMock(Connection::class);

        $extension->expects($this->once())
            ->method('getConnection')
            ->willReturn($mockCon);

        $extension->expects($this->once())
            ->method('isConnected')
            ->willReturn(false);

        $container->expects($this->never())
            ->method('getExtensionConfig');

        $extension->prepend($container);
    }

    public function testPrependSkipsWhenPluginDisabled()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $extension = $this->getMockBuilder(ResizeImageExtension::class)
            ->onlyMethods(['getConnection', 'isConnected', 'isPluginEnabled'])
            ->getMock();

        $mockCon = $this->createMock(Connection::class);

        $extension->method('getConnection')->willReturn($mockCon);
        $extension->method('isConnected')->willReturn(true);
        $extension->method('isPluginEnabled')->willReturn(false);

        $container->expects($this->never())
            ->method('getExtensionConfig');

        $extension->prepend($container);
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
            ->onlyMethods(['getExtensionConfig', 'resolveEnvPlaceholders', 'getParameter'])
            ->disableOriginalConstructor()
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
