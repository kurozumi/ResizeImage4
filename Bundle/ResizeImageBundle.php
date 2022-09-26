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

namespace Plugin\ResizeImage42\Bundle;

use Plugin\ResizeImage42\DependencyInjection\ResizeImageExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ResizeImageBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ResizeImageExtension();
    }
}
