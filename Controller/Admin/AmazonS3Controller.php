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

namespace Plugin\ResizeImage42\Controller\Admin;


use Aws\S3\S3Client;
use Eccube\Controller\AbstractController;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Plugin\ResizeImage42\Form\Type\Admin\AmazonS3\BucketType;
use Plugin\ResizeImage42\Form\Type\Admin\AmazonS3\ConfigType;
use Plugin\ResizeImage42\Form\Type\Admin\AmazonS3\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package Plugin\ResizeImage42\Controller\Admin
 *
 * @Route("/%eccube_admin_route%/resize_image/amazon_s3")
 */
class AmazonS3Controller extends AbstractController
{
    /**
     * @param Request $request
     * @param CacheUtil $cacheUtil
     * @return array | RedirectResponse
     *
     * @Route("/user", name="admin_resize_image_amazon_s3_user")
     * @Template("@ResizeImage42/admin/AmazonS3/user.twig")
     */
    public function user(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->createForm(UserType::class, [
            'access_key_id' => getenv('AWS_ACCESS_KEY_ID'),
            'secret_access_key' => getenv('AWS_SECRET_ACCESS_KEY'),
            'region' => getenv('AWS_S3_REGION') ? getenv('AWS_S3_REGION') : $this->getParameter('aws_s3_region'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->replaceOrAddEnv([
                'AWS_ACCESS_KEY_ID' => $data['access_key_id'],
                'AWS_SECRET_ACCESS_KEY' => $data['secret_access_key'],
                'AWS_S3_REGION' => $data['region']
            ]);

            $cacheUtil->clearCache();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_resize_image_amazon_s3_bucket');
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @param Request $request
     * @param CacheUtil $cacheUtil
     * @return array | RedirectResponse
     *
     * @Route("/bucket", name="admin_resize_image_amazon_s3_bucket")
     * @Template("@ResizeImage42/admin/AmazonS3/bucket.twig")
     */
    public function bucket(Request $request, CacheUtil $cacheUtil, S3Client $client)
    {
        try {
            $buckets = $client->listBuckets();
            $buckets = array_map(function ($bucket) {
                return $bucket['Name'];
            }, $buckets['Buckets']);
        } catch (\Exception $e) {
            $this->addError('AWS アクセスキーを設定してください', 'admin');
            return $this->redirectToRoute('admin_resize_image_amazon_s3_user');
        }

        $options['buckets'] = array_combine($buckets, $buckets);
        $form = $this->createForm(BucketType::class, [
            'bucket' => getenv('AWS_S3_BUCKET')
        ], $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->replaceOrAddEnv([
                'AWS_S3_BUCKET' => $data['bucket']
            ]);

            $cacheUtil->clearCache();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_resize_image_amazon_s3');
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @param Request $request
     * @param CacheUtil $cacheUtil
     * @return array | RedirectResponse
     *
     * @Route("", name="admin_resize_image_amazon_s3")
     * @Template("@ResizeImage42/admin/AmazonS3/index.twig")
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->createForm(ConfigType::class, [
            'enabled' => (bool)getenv('AWS_S3_ENABLED'),
            'cache_control' => getenv('AWS_S3_CACHE_CONTROL') ? getenv('AWS_S3_CACHE_CONTROL') : $this->getParameter('aws_s3_cache_control')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->replaceOrAddEnv([
                'AWS_S3_ENABLED' => (int)$data['enabled'],
                'AWS_S3_CACHE_CONTROL' => $data['cache_control']
            ]);

            $cacheUtil->clearCache();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_resize_image_amazon_s3');
        }

        return [
            'form' => $form->createView(),
            'access_key_id' => getenv('AWS_ACCESS_KEY_ID'),
            'bucket' => getenv('AWS_S3_BUCKET')
        ];
    }

    private function replaceOrAddEnv(array $replacement)
    {
        $envFile = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . '.env';
        if (file_exists($envFile)) {
            $env = file_get_contents($envFile);
            $env = StringUtil::replaceOrAddEnv($env, $replacement);
            file_put_contents($envFile, $env);
        }
    }
}
