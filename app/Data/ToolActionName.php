<?php

namespace App\Data;

enum ToolActionName: string 
{
    case CompressImage = 'compress_image';
    case DummyAction = 'dummy_action';
    case PdfConvertToJpeg = "pdf_convert_to_jpeg";
    case PdfCompress = 'pdf_compress';

    /**
     * Is this action PDF related.
     */
    public function isPdfRelated(): bool 
    {
        return str_starts_with($this->value, "pdf_");
    }
}
