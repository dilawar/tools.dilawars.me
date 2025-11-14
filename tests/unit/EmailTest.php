<?php

declare(strict_types=1);

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

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;

final class EmailTest extends CIUnitTestCase
{
    /**
     * @group: Email
     */
    public function testSendEmail(): void
    {
        $smtp = service('smtp');
        $smtp->sendEmail(
            'dilawar.s.rajput@gmail.com',
            'test email',
            '<p>Testing '.random_int(0, mt_getrandmax()).'</p>'
        );
    }
}
