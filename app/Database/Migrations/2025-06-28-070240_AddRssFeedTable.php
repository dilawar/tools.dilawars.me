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

class AddRssFeedTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'feed_source'     => ['type' => 'VARCHAR', 'constraint' => 258, 'null' => false],
            'title'           => ['type' => 'VARCHAR', 'constraint' => 512, 'null' => true],
            'link'            => ['type' => 'TEXT', 'null' => false],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'guid'            => ['type' => 'VARCHAR', 'constraint' => 512, 'null' => true],
            'guid_is_permalink' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true],
            'content'         => ['type' => 'TEXT', 'null' => true],
            'author'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'category'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'enclosure_url'   => ['type' => 'TEXT', 'null' => true],
            'enclosure_type'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'publication_date' => ['type' => 'DATETIME', 'null' => false],
            'timestamp'       => ['type' => 'DATETIME', 'null' => false],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['feed_source', 'guid']);
        $this->forge->addKey('feed_source');
        $this->forge->createTable('rss_feed_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('rss_feed_items');
    }
}
