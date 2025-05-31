<?php

namespace App\Cells;

use App\Data\ImageData;
use CodeIgniter\View\Cells\Cell;

class DownloadFileCell extends Cell
{
    /**
     * @var array<ImageData>
     */
    public array $images = [];
}
