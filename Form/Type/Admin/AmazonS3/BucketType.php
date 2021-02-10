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

namespace Plugin\ResizeImage4\Form\Type\Admin\AmazonS3;


use Aws\S3\S3Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class BucketType extends AbstractType
{
    /**
     * @var S3Client
     */
    private $client;

    public function __construct(S3Client $client)
    {
        $this->client = $client;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $buckets = $this->client->listBuckets();
        $buckets = array_map(function ($bucket){
            return $bucket['Name'];
        }, $buckets['Buckets']);

        $builder
            ->add('bucket', ChoiceType::class, [
                'choices' => array_combine($buckets, $buckets)
            ]);
    }
}
