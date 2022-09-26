<?php
/**
 * This file is part of ResizeImage42
 *
 * Copyright(c) Akira Kurozumi <info@a-zumi.net>
 *
 * https://a-zumi.net
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ResizeImage42\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class ResizeImageExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $plugins = $container->getParameter('eccube.plugins.enabled');

        if (!in_array('ResizeImage42', $plugins)) {
            return;
        }

        $extensionConfigsRefl = new \ReflectionProperty(ContainerBuilder::class, 'extensionConfigs');
        $extensionConfigsRefl->setAccessible(true);
        $extensionConfigs = $extensionConfigsRefl->getValue($container);

        foreach ($extensionConfigs["liip_imagine"] as $key => $liip_imagine) {
            if (isset($liip_imagine["filter_sets"])) {
                if ((bool)getenv('AWS_S3_ENABLED')) {
                    $extensionConfigs["liip_imagine"][$key]["filter_sets"]["resize"]["cache"] = "aws_s3_resolver";
                } else {
                    $extensionConfigs["liip_imagine"][$key]["filter_sets"]["resize"]["cache"] = null;
                }
            }
        }

        $extensionConfigsRefl->setValue($container, $extensionConfigs);
    }

    public function load(array $configs, ContainerBuilder $container)
    {
    }
}
