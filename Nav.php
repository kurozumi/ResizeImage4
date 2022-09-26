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

namespace Plugin\ResizeImage42;


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
                    'resize_image_config_amazon_s3' => [
                        'name' => 'Amazon S3連携設定',
                        'url' => 'admin_resize_image_amazon_s3'
                    ]
                ]
            ]
        ];
    }
}
