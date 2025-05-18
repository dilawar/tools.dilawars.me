<?php

namespace App\Database\Seeds;

use App\Data\StatsName;
use CodeIgniter\Database\Seeder;

class StatsSeeder extends Seeder
{
    public function run(): void
    {
        StatsName::initialize();
    }
}
