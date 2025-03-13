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

namespace Plugin\ResizeImage42\Factory;

use Aws\S3\S3Client;

class AmazonS3ClientFactory
{
    /**
     * @param array $config
     *
     * @return S3Client
     */
    public static function create(array $config = []): S3Client
    {
        return new S3Client($config);
    }
}
