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

namespace Plugin\ResizeImage42\Tests\Form\Admin;


use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Plugin\ResizeImage42\Form\Type\Admin\AmazonS3\ConfigType;

class ConfigTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array
     */
    protected $formData = [
        'enabled' => true,
        'cache_control' => 86400
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(ConfigType::class, null, [
                'csrf_protection' => false
            ])
            ->getForm();

        putenv('AWS_ACCESS_KEY_ID=dummy');
        putenv('AWS_SECRET_ACCESS_KEY=dummy');
        putenv('AWS_S3_REGION=dummy');
        putenv('AWS_S3_BUCKET=dummy');
    }

    public function test正常データ()
    {
        $this->form->submit($this->formData);
        self::assertTrue($this->form->isValid());
    }

    public function testS3有効の場合は他の項目が空だとエラー()
    {
        $this->formData['enabled'] = true;
        $this->formData['cache_control'] = '';
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }

    public function testCacheControlが1以上だったらOK()
    {
        $this->formData['cache_control'] = 1;
        $this->form->submit($this->formData);
        self::assertTrue($this->form->isValid());
    }

    public function testCacheControlが0だったら駄目()
    {
        $this->formData['cache_control'] = 0;
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }

    public function testCacheControlが0未満だったら駄目()
    {
        $this->formData['cache_control'] = -1;
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }

    public function testCacheControlが数値じゃなかったらエラー()
    {
        $this->formData['cache_control'] = 'sss';
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }
}
