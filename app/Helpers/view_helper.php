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

function glossaryImageHeic(): string
{
    return _glossary(
        'HEIC',
        'https://en.wikipedia.org/wiki/High_Efficiency_Image_File_Format'
    );
}

function glossaryImageJpeg(): string
{
    return _glossary('JPEG', 'https://en.wikipedia.org/wiki/JPEG');
}

function _glossary(string $label, string $url): string
{
    return "<a class='glossary' 
        target='_blank' href='{$url}'>{$label}</a>";
}

function pill(string $text): string
{
    return sprintf("<span class='badge badge-pill bg-info'>%s</span>", $text);
}

function showFilesize(int $sizeInBytes): string
{
    $kb = floatval($sizeInBytes) / 1024;
    if ($kb < 1024.0) {
        return number_format($kb, 1).' KB';
    }

    $mb = $kb / 1024.0;
    if ($mb < 1024.0) {
        return number_format($mb, 1).' MB';
    }

    $gb = $mb / 1024.0;

    return number_format($gb, 1).' GB';

}

function formInputBootstrap(string $id, string $label, string $value, string $type = 'text'): string
{
    $html[] = "<div class='form-group row'>";
    $html[] = _labelColumnBootstrap($label, $id);

    $html[] = "<div class='col-12 col-sm-4'>";
    $html[] = form_input($id, value: $value, type: $type, extra: [
        'id' => $id,
        'class' => 'form-control',
    ]);
    $html[] = '</div>';
    $html[] = '</div>';

    return implode(' ', $html);
}

/**
 * @param array<string, string> $options
 */
function formSelectBootstrap(string $id, string $label, string $value, array $options): string
{
    $html[] = "<div class='row form-group'>";
    $html[] = _labelColumnBootstrap($label, $id);

    $html[] = "<div class='col-4'>";
    $html[] = form_dropdown($id, $options, $value, extra: [
        'id' => $id,
        'class' => 'form-control',
    ]);
    $html[] = '</div>';
    $html[] = '</div>';

    return implode(' ', $html);
}

/**
 * Render a submit button.
 */
function submitButton(
    string $label = 'Upload',
    string $extraClass = '',
    ?string $divClass = null,
): string {
    $html = ['<div class="row form-group mt-1">'];
    $html[] = _labelColumnBootstrap('', '');
    if ($divClass) {
        $html[] = sprintf("<div class='%s'>", $divClass);
    }

    $html[] = form_submit('submit', $label, extra: [
        'class' => $extraClass.' btn btn-primary',
    ]);

    if ($divClass) {
        $html[] = '</div>';
    }

    $html[] = '</div>';

    return implode(' ', $html);
}

function _labelColumnBootstrap(string $label, string $id): string
{
    return sprintf("<label for='%s' class='col-12 col-sm-5 col-form-label'>%s</label>", $id, $label);
}

function formUploadFile(
    string $name,
    string $label,
    string $accept = '*',
    string $extra = '',
): string {
    $html[] = "<div class='d-flex row'>";

    $html[] = _labelColumnBootstrap($label, $name);

    $html[] = "<div class='col-12 col-sm-6'>";
    $html[] = form_upload($name, extra: [
        'class' => 'form-control',
        'accept' => $accept,
        'extra' => $extra,
    ]);
    $html[] = '</div>'; // col
    $html[] = '</div>'; // row

    return implode(' ', $html);

}

function iconify(string $iconName, string $tooltip, int $size = 32): string
{
    return "<span title='{$tooltip}'>
        <iconify-icon icon='{$iconName}' width='{$size}'></iconify-icon>
    </span>";

}

/**
 * @brief Convert given datetime/timestamp to HTML input with type
 * datetime-local.
 */
function htmlDatetimeLocal(string $datetime): string
{
    return date('Y-m-d\TH:i', intval(strtotime($datetime)));
}

/**
 * A link target.
 */
function a(string $href, ?string $label = null, string $target = '_self'): string
{
    return sprintf("<a href='%s' _target='%s'>", $href, $target).($label ?? $href).'</a>';
}

/**
 * Show compression statistics.
 */
function compressionStats(int $downloadSize, int $uploadSize): string
{
    $html[] = '';

    $perc = floatval($downloadSize) / floatval($uploadSize) * 100.0;
    // $xTimes = number_format(100.0 / $perc, 1);

    if ($downloadSize < $uploadSize) {
        $html[] = 'Uploaded file of size '.showFilesize($uploadSize).' is compressed to <strong>'.showFilesize($downloadSize).'</strong>.';
        $html[] = ' Compression ratio <strong>'.number_format(100.0 / $perc, 1).'</strong>.';
    } else {
        $html[] = "<p class='text-warning'>We ended up increasing the file size from!</p>";
    }

    $html[] = '</p>';

    return implode('', $html);

}
