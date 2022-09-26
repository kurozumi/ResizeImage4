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

namespace Plugin\ResizeImage42\Form\Type\Admin\AmazonS3;


use Eccube\Form\Type\ToggleSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', ToggleSwitchType::class)
            ->add('cache_control', NumberType::class, [
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 1
                    ])
                ]
            ]);

        $builder
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                if (false === $form->get('enabled')->getData()) {
                    return;
                }

                if (null === $form->get('cache_control')->getData()) {
                    $form->get('cache_control')->addError(new FormError('入力してください。'));
                }

                if (
                    !getenv('AWS_ACCESS_KEY_ID') ||
                    !getenv('AWS_SECRET_ACCESS_KEY') ||
                    !getenv('AWS_S3_REGION')
                ) {
                    $form->addError(new FormError('AWS アクセスキーが設定されていないので有効化できません。'));
                }

                if (
                    !getenv('AWS_S3_BUCKET')
                ) {
                    $form->addError(new FormError('バケットが設定されていないので有効化できません。'));
                }
            });
    }
}
