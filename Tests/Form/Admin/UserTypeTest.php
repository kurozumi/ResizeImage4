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
use Plugin\ResizeImage42\Form\Type\Admin\AmazonS3\UserType;

class UserTypeTest extends AbstractTypeTestCase
{
    protected $form;

    protected $formData = [
        'access_key_id' => 'dummy',
        'secret_access_key' => 'dummy',
        'region' => 'dummy',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(UserType::class, null, [
                'csrf_protection' => false
            ])
            ->getForm();
    }

    public function test正常データ()
    {
        $this->form->submit($this->formData);
        self::assertTrue($this->form->isValid());
    }

    public function testAccessKeyIdが空はエラー()
    {
        $this->formData['access_key_id'] = '';
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }

    public function testSecretAccessKeyが空はエラー()
    {
        $this->formData['secret_access_key'] = '';
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }

    public function testRegionが空はエラー()
    {
        $this->formData['region'] = '';
        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }
}
