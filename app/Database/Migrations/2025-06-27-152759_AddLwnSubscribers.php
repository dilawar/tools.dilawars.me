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
    public function up()
    {
        $this->db->query('CREATE TABLE subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(100),
            service_name VARCHAR(100) NOT NULL,
            subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            confirmation_token VARCHAR(256),
            confirmed_at DATETIME,
            is_active BOOLEAN DEFAULT FALSE,
            unsubscribe_reason TEXT,
            tags JSON,
            INDEX (email)
        )');
    }

    public function down()
    {
        $this->db->query('DROP TABLE subscribers');
    }
}
