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
use Plugin\ResizeImage42\Form\Type\Admin\AmazonS3\BucketType;

class BucketTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array
     */
    protected $formData = [
        'bucket' => 'dummy'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(BucketType::class, null, [
                'csrf_protection' => false,
                'buckets' => ['dummy' => 'dummy']
            ])
            ->getForm();

        putenv('AWS_ACCESS_KEY_ID=dummy');
        putenv('AWS_SECRET_ACCESS_KEY=dummy');
        putenv('AWS_S3_REGION=dummy');
    }

    public function test正常データ()
    {
        $this->form->submit($this->formData);
        self::assertTrue($this->form->isValid());
    }
}
