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

namespace App\Data;

enum ToolActionName: string
{
    case CompressImage = 'compress_image';
    case DummyAction = 'dummy_action';
    case PdfConvertToJpeg = 'pdf_convert_to_jpeg';
    case PdfCompress = 'pdf_compress';

    /**
     * Is this action PDF related.
     */
    public function isPdfRelated(): bool
    {
        return str_starts_with($this->value, 'pdf_');
    }
}
