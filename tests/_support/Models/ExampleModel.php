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

namespace Tests\Support\Models;

use CodeIgniter\Model;

class ExampleModel extends Model
{
    protected $table = 'factories';

    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'uid',
        'class',
        'icon',
        'summary',
    ];

    protected $useTimestamps = true;

    protected $validationRules = [];

    protected $validationMessages = [];

    protected $skipValidation = false;
}
