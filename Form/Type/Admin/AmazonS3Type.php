<?php
/**
 * This file is part of ResizeImage4
 *
 * Copyright(c) Akira Kurozumi <info@a-zumi.net>
 *
 * https://a-zumi.net
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ResizeImage4\Form\Type\Admin;


use Eccube\Form\Type\ToggleSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AmazonS3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', ToggleSwitchType::class)
            ->add('access_key_id', TextType::class, [
                'required' => false
            ])
            ->add('secret_access_key', TextType::class, [
                'required' => false
            ])
            ->add('bucket', TextType::class, [
                'required' => false
            ])
            ->add('region', TextType::class, [
                'required' => false
            ])
            ->add('cache_control', TextType::class, [
                'required' => false
            ]);

        $builder
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                if (false === $form->get('enabled')->getData()) {
                    return;
                }

                if (null === $form->get('access_key_id')->getData()) {
                    $form->get('access_key_id')->addError(new FormError('入力してください。'));
                }

                if (null === $form->get('secret_access_key')->getData()) {
                    $form->get('secret_access_key')->addError(new FormError('入力してください。'));
                }

                if (null === $form->get('bucket')->getData()) {
                    $form->get('bucket')->addError(new FormError('入力してください。'));
                }

                if (null === $form->get('region')->getData()) {
                    $form->get('region')->addError(new FormError('入力してください。'));
                }

                if (null === $form->get('cache_control')->getData()) {
                    $form->get('cache_control')->addError(new FormError('入力してください。'));
                }
            });
    }
}
