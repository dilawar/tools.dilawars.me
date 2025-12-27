<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Config;

use App\Services\EmailService;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * SMTP service.
     */
    public static function smtp(bool $getShared = true): EmailService
    {
        if ($getShared) {
            /**
             * @var EmailService
             */
            $smtp = static::getSharedInstance('smtp');

            return $smtp;
        }

        return new EmailService();
    }

    /**
     * Templeate rendering using TWIG.
     */
    public static function twig(bool $getShared = true): \Twig\Environment
    {
        if ($getShared) {
            /**
             * @var \Twig\Environment
             */
            $twig = static::getSharedInstance('twig');

            return $twig;
        }

        $filesystemLoader = new \Twig\Loader\FilesystemLoader(APPPATH.'/Views/templates');

        return new \Twig\Environment($filesystemLoader);
    }
}
