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

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLwnSubscribers extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'email'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'name'                => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'service_name'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'subscribed_at'       => ['type' => 'DATETIME', 'null' => true],
            'confirmation_token'  => ['type' => 'VARCHAR', 'constraint' => 256, 'null' => true],
            'confirmed_at'        => ['type' => 'DATETIME', 'null' => true],
            'is_active'           => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => 0],
            'unsubscribe_reason'  => ['type' => 'TEXT', 'null' => true],
            'tags'                => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('subscribers');
    }

    public function down(): void
    {
        $this->forge->dropTable('subscribers');
    }
}
