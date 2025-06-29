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
    public function up()
    {
        $this->db->query("CREATE TABLE kv_store(
            key_name VARCHAR(255) NOT NULL,
            key_subname VARCHAR(512) NOT NULL DEFAULT '',
            value_text TEXT DEFAULT '',
            value_int INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE(key_name, key_subname)
        );
        ");
    }

    public function down()
    {
        $this->forge->dropTable('kv_store');
    }
}
