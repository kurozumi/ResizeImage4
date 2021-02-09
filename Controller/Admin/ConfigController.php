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

namespace Plugin\ResizeImage4\Controller\Admin;


use Dotenv\Dotenv;
use Eccube\Controller\AbstractController;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Plugin\ResizeImage4\Form\Type\Admin\AmazonS3Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package Plugin\ResizeImage4\Controller\Admin
 *
 * @Route("/%eccube_admin_route%/resize_image/config")
 */
class ConfigController extends AbstractController
{
    /**
     * @param Request $request
     * @param CacheUtil $cacheUtil
     * @return array
     *
     * @Route("/aws", name="admin_resize_image_config_aws")
     * @Template("@ResizeImage4/admin/Config/aws.twig")
     */
    public function aws(Request $request, CacheUtil $cacheUtil)
    {
        (new Dotenv($this->getParameter('kernel.project_dir')))->load();

        $form = $this->createForm(AmazonS3Type::class, [
            'enabled' => (bool)getenv('AWS_S3_ENABLED'),
            'access_key_id' => getenv('AWS_ACCESS_KEY_ID'),
            'secret_access_key' => getenv('AWS_SECRET_ACCESS_KEY'),
            'bucket' => getenv('AWS_S3_BUCKET'),
            'region' => getenv('AWS_S3_REGION') ? getenv('AWS_S3_REGION') : $this->getParameter('aws_s3_region'),
            'cache_control' => getenv('AWS_S3_CACHE_CONTROL') ? getenv('AWS_S3_CACHE_CONTROL') : $this->getParameter('aws_s3_cache_control')
        ]);
        $form->handleRequest($request);

        var_dump($this->getParameter('aws_s3_cache_control'));

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $envFile = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . '.env';
            if (file_exists($envFile)) {
                $env = file_get_contents($envFile);
                $env = StringUtil::replaceOrAddEnv($env, [
                    'AWS_S3_ENABLED' => (int)$data['enabled'],
                    'AWS_ACCESS_KEY_ID' => $data['access_key_id'],
                    'AWS_SECRET_ACCESS_KEY' => $data['secret_access_key'],
                    'AWS_S3_BUCKET' => $data['bucket'],
                    'AWS_S3_REGION' => $data['region'],
                    'AWS_S3_CACHE_CONTROL' => $data['cache_control']
                ]);
                file_put_contents($envFile, $env);
            }

            $cacheUtil->clearCache();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_resize_image_config_aws');
        }

        return [
            'form' => $form->createView()
        ];
    }
}
