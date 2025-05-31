<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class DownloadFileCell extends Cell
{
    /**
     * @var array<string>
     */
    public array $thumbnails = [];

    /**
     * @var array<string>
     */
    public array $downloads = [];
}
