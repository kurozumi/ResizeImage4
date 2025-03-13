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

namespace Plugin\ResizeImage42\Tests\Web\Admin;

use Eccube\Common\Constant;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use function PHPUnit\Framework\assertTrue;

class AmazonS3ControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var string
     */
    protected $envFile;

    /**
     * @var string
     */
    protected $env;

    protected function setUp(): void
    {
        parent::setUp();

        $member = $this->createMember();
        $this->loginTo($member);

        $this->envFile = static::getContainer()->getParameter('kernel.project_dir').'/.env';
        if (file_exists($this->envFile)) {
            $this->env = file_get_contents($this->envFile);
        }
    }

    protected function tearDown(): void
    {
        if ($this->env) {
            file_put_contents($this->envFile, $this->env);
        }

        parent::tearDown();
    }

    public function testアクセスユーザーページで情報を保存したらenvファイルに追記されるか()
    {
        $this->client->request('POST', $this->generateUrl('admin_resize_image_amazon_s3_user'), [
            'user' => [
                'access_key_id' => 'dummy',
                'secret_access_key' => 'dummy',
                'region' => 'dummy',
                Constant::TOKEN_NAME => 'dummy',
            ],
        ]);
        self::assertTrue($this->client->getResponse()->isRedirection());

        $keys = [
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
            'AWS_S3_REGION',
        ];

        foreach ($keys as $key) {
            $pattern = '/^('.$key.')=(.*)/m';
            if (preg_match($pattern, file_get_contents($this->envFile), $matches)) {
                self::assertEquals('dummy', $matches[2]);
            } else {
                self::fail(sprintf('%sが見つかりませんでした。', $key));
            }
        }
    }

    public function test設定ページで情報を保存したらenvファイルに追記されるか()
    {
        putenv('AWS_ACCESS_KEY_ID=dummy');
        putenv('AWS_SECRET_ACCESS_KEY=dummy');
        putenv('AWS_S3_REGION=dummy');
        putenv('AWS_S3_BUCKET=dummy');

        $this->client->request('POST', $this->generateUrl('admin_resize_image_amazon_s3'), [
            'config' => [
                'enabled' => true,
                'cache_control' => 1,
                Constant::TOKEN_NAME => 'dummy',
            ],
        ]);
        self::assertTrue($this->client->getResponse()->isRedirection());

        $keys = [
            'AWS_S3_ENABLED',
            'AWS_S3_CACHE_CONTROL',
        ];


        foreach ($keys as $key) {
            $pattern = '/^('.$key.')=(.*)/m';
            if (preg_match($pattern, file_get_contents($this->envFile), $matches)) {
                self::assertEquals(1, $matches[2]);
            } else {
                self::fail(sprintf('%sが見つかりませんでした。', $key));
            }
        }
    }
}
