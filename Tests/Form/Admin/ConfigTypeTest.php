<?php


namespace Plugin\ResizeImage4\Tests\Form\Admin;


use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Eccube\Util\StringUtil;
use Plugin\ResizeImage4\Form\Type\Admin\AmazonS3\ConfigType;
use Symfony\Component\Filesystem\Filesystem;

class ConfigTypeTest extends AbstractTypeTestCase
{
    protected $form;

    protected $formData = [
        'enabled' => true,
        'cache_control' => 86400
    ];

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->form = $this->formFactory
            ->createBuilder(ConfigType::class, null, [
                'csrf_protection' => false
            ])
            ->getForm();

        putenv('AWS_ACCESS_KEY_ID', 'dummy');
        putenv('AWS_SECRET_ACCESS_KEY', 'dummy');
        putenv('AWS_S3_REGION', 'dummy');
        putenv('AWS_S3_BUCKET', 'dummy');
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function test正常データ()
    {
        $this->form->submit($this->formData);
        self::assertTrue($this->form->isValid());
    }

    public function testS3有効の場合は他の項目が空だとエラー()
    {
        $this->formData['enabled'] = true;
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
