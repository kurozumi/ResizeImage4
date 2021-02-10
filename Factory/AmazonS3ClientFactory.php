<?php


namespace Plugin\ResizeImage4\Factory;


use Aws\S3\S3Client;

class AmazonS3ClientFactory
{
    public static function create(array $config = [])
    {
        return new S3Client($config);
    }
}
