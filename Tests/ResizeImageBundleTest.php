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

namespace Plugin\ResizeImage42\Tests;

use Plugin\ResizeImage42\Bundle\ResizeImageBundle;
use Plugin\ResizeImage42\DependencyInjection\ResizeImageExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResizeImageBundleTest extends KernelTestCase
{
    public function testGetContainerExtension(): void
    {
        $bundle = new ResizeImageBundle();
        $extension = $bundle->getContainerExtension();
        self::assertInstanceOf(ResizeImageExtension::class, $extension);
    }
}
