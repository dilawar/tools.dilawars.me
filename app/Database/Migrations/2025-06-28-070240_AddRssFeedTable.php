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
    public function up()
    {
        // add table to aggregate RSS feeds.
        $query = <<<'QUERY'
        CREATE TABLE rss_feed_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        feed_source VARCHAR(258) NOT NULL,
        title VARCHAR(512),
        link TEXT NOT NULL,
        description TEXT,

        guid VARCHAR(512), 
        guid_is_permalink BOOLEAN,

        content BLOB,
        author VARCHAR(255), 
        category VARCHAR(255),
        enclosure_url TEXT,
        enclosure_type VARCHAR(255),

        publication_date DATETIME NOT NULL,
        timestamp TIMESTAMP NOT NULL,

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(feed_source, guid),
        INDEX(feed_source)) 
QUERY;
        $this->db->query($query);
    }

    public function down()
    {
        $this->db->query('DROP TABLE rss_feed_items');
    }
}
