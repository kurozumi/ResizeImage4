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

namespace Plugin\ResizeImage42\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class ResizeImageExtension extends Extension implements PrependExtensionInterface
{
    public const PLUGIN_CODE = 'ResizeImage42';

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container): void
    {
        $conn = $this->getConnection($container);
        if (false === $this->isConnected($conn)) {
            return;
        }

        if (false === $this->isPluginEnabled($conn)) {
            return;
        }

        $extensionConfigsRefl = new \ReflectionProperty(ContainerBuilder::class, 'extensionConfigs');
        $extensionConfigsRefl->setAccessible(true);
        $extensionConfigs = $extensionConfigsRefl->getValue($container);

        foreach ($extensionConfigs['liip_imagine'] as $key => $liip_imagine) {
            if (isset($liip_imagine['filter_sets'])) {
                if ((bool) getenv('AWS_S3_ENABLED')) {
                    $extensionConfigs['liip_imagine'][$key]['filter_sets']['resize']['cache'] = 'aws_s3_resolver';
                } else {
                    $extensionConfigs['liip_imagine'][$key]['filter_sets']['resize']['cache'] = null;
                }
            }
        }

        $extensionConfigsRefl->setValue($container, $extensionConfigs);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return Connection
     *
     * @throws Exception
     */
    protected function getConnection(ContainerBuilder $container): Connection
    {
        // doctrine.yml, または他のprependで差し込まれたdoctrineの設定値を取得する.
        $configs = $container->getExtensionConfig('doctrine');

        // $configsは, env変数(%env(xxx)%)やパラメータ変数(%xxx.xxx%)がまだ解決されていないため, resolveEnvPlaceholders()で解決する
        // @see https://github.com/symfony/symfony/issues/22456
        $configs = $container->resolveEnvPlaceholders($configs, true);

        // doctrine bundleのconfigurationで設定値を正規化する.
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        // prependのタイミングではコンテナのインスタンスは利用できない.
        // 直接dbalのconnectionを生成し, dbアクセスを行う.
        $params = $config['dbal']['connections'][$config['dbal']['default_connection']];
        // ContainerInterface::resolveEnvPlaceholders() で取得した DATABASE_URL は
        // % がエスケープされているため、環境変数から取得し直す
        $params['url'] = env('DATABASE_URL');

        return DriverManager::getConnection($params);
    }

    /**
     * @param Connection $conn
     *
     * @return bool
     */
    protected function isConnected(Connection $conn): bool
    {
        try {
            if (!$conn->executeQuery('select 1')) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        try {
            $tableNames = $conn->createSchemaManager()->listTableNames();
        } catch (\Exception $e) {
            return false;
        }

        return in_array('dtb_plugin', $tableNames, true);
    }

    /**
     * プラグインが有効化されているかチェック
     *
     * @param Connection $conn
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function isPluginEnabled(Connection $conn): bool
    {
        $stmt = $conn->executeQuery('select count(*) from dtb_plugin where code = ? and enabled = ?', [self::PLUGIN_CODE, 1]);

        return $stmt->fetchOne() > 0;
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
    }
}
