<?php

function glossaryImageHeic(): string
{
    return _glossary(
        'HEIC',
        "https://en.wikipedia.org/wiki/High_Efficiency_Image_File_Format"
    );
}

function glossaryImageJpeg(): string
{
    return _glossary('JPEG', "https://en.wikipedia.org/wiki/JPEG");
}

function _glossary(string $label, string $url): string {
    return "<a class='glossary' 
        target='_blank' href='$url'>$label</a>";
}

function pill(string $text): string 
{
    return "<span class='badge badge-pill bg-info'>$text</span>";
}

function showFilesize(int $sizeInBytes): string 
{
    $kb = floatval($sizeInBytes) / 1024;
    if($kb < 1024.0) {
        return number_format($kb, 1) . " KB";
    }

    $mb = $kb / 1024.0;
    if($mb < 1024.0) {
        return number_format($mb, 1) . " MB";
    }

    $gb = $mb / 1024.0;
    return number_format($gb, 1) . " GB";

}

function formInputBootstrap(string $id, string $label, string $value, string $type = 'text'): string 
{
    $html[] = "<div class='form-group row'>";
    $html[] = "<label for='$id' class='col-5 col-form-label'>$label</label>";

    $html[] = "<div class='col-4'>";
    $html[] = form_input($id, value: $value, type: $type, extra: [
        'id' => $id,
        'class' => 'form-control',
    ]);
    $html[] = "</div>";
    $html[] = "</div>";

    return implode(' ', $html);
}

/**
 * @param array<string, string> $options
 */
function formSelectBootstrap(string $id, string $label, string $value, array $options): string 
{
    $html[] = "<div class='row form-group'>";
    $html[] = "<label for='$id' class='col-5 col-form-label'>$label</label>";

    $html[] = "<div class='col-4'>";
    $html[] = form_dropdown($id, $options, $value, extra: [
        'id' => $id,
        'class' => 'form-control',
    ]);
    $html[] = "</div>";
    $html[] = "</div>";

    return implode(' ', $html);
}
