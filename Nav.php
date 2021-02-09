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

namespace Plugin\ResizeImage4;


use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{

    public static function getNav()
    {
        return [
            'resize_iamge_config' => [
                'name' => '画像リサイズ管理',
                'icon' => 'fa-picture-o',
                'children' => [
                    'amazon_s3_config' => [
                        'name' => 'Amazon S3連携設定',
                        'url' => 'admin_resize_image_config_aws'
                    ]
                ]
            ]
        ];
    }
}
