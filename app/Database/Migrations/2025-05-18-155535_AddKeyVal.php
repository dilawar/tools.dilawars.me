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

class AddStat extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'key_name'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'key_subname' => ['type' => 'VARCHAR', 'constraint' => 512, 'null' => false, 'default' => ''],
            'value_text'  => ['type' => 'TEXT', 'null' => true],
            'value_int'   => ['type' => 'INT', 'null' => true, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addUniqueKey(['key_name', 'key_subname']);
        $this->forge->createTable('kv_store');
    }

    public function down(): void
    {
        $this->forge->dropTable('kv_store');
    }
}
