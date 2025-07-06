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

namespace App\Models;

use CodeIgniter\Model;

class LwnSubscriber extends Model
{
    protected $table = 'subscribers';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $useSoftDeletes = false;

    protected $protectFields = true;

    protected $allowedFields = [
        'email',
        'name',
        'service_name',
        'subscribed_at',
        'confirmation_token',
        'confirmed_at',
        'is_active',
        'unsubscrive_reason',
        'tags',
    ];

    protected bool $allowEmptyInserts = false;

    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'subscribers' => 'datetime',
        'is_active' => 'boolean',
        'tags' => 'json-array',
    ];

    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;

    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];

    protected $validationMessages = [];

    protected $skipValidation = false;

    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    protected $beforeInsert = [];

    protected $afterInsert = [];

    protected $beforeUpdate = [];

    protected $afterUpdate = [];

    protected $beforeFind = [];

    protected $afterFind = [];

    protected $beforeDelete = [];

    protected $afterDelete = [];
}
