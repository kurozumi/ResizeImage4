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

namespace Plugin\ResizeImage42\Factory;


use Aws\S3\S3Client;

class AmazonS3ClientFactory
{
    public static function create(array $config = [])
    {
        return new S3Client($config);
    }
}
