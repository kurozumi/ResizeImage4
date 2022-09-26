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


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('access_key_id', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('secret_access_key', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('region', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }
}
