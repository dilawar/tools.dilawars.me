<?php

echo $this->extend('default');
echo $this->section('content');

// @phpstan-ignore variable.undefined
$toFormat = $to;

// From format.
$fromFormat = $from ?? '*';

$thumbnailUri = $thumbnail ?? null;
$downloadUrl = $download_url ?? null;
$convertedFileFilename = $converted_file_filename ?? '';

/**
 * @var array<string>
 */
$supportedFormats = supportedImageFormats();

if (! function_exists('_renderUploadFormInner')) {

    /**
     * @param array<string> $formats
     */
    function _renderUploadFormInner(string $toFormat, string $fromFormat, array $formats): string
    {
        $imageFormats = [];
        foreach ($formats as $format) {
            $format = strtolower($format);
            $imageFormats[$format] = $format;
        }

        $accept = '' !== $fromFormat && '*' !== $fromFormat ? '.'.$fromFormat : 'image/*';

        $html = [];

        // File picker — full width
        $html[] = "<div class='mb-3'>";
        $html[] = "<label class='form-label fw-semibold' for='img-upload'>Image file</label>";
        $html[] = form_input('image', type: 'file', extra: [
            'id'     => 'img-upload',
            'class'  => 'form-control',
            'accept' => $accept,
        ]);
        $html[] = '</div>';

        // Format selector + submit — same row, aligned to bottom
        $html[] = "<div class='row g-3 align-items-end'>";

        $html[] = "<div class='col-sm-4'>";
        $html[] = "<label class='form-label fw-semibold' for='".SELECTIZE_ID_PREFIX."_to_format'>Output format</label>";
        $html[] = form_dropdown(
            'to_format',
            options: $imageFormats,
            selected: $toFormat,
            extra: [
                'id'    => SELECTIZE_ID_PREFIX.'_to_format',
                'class' => 'form-select',
            ],
        );
        $html[] = '</div>';

        $html[] = "<div class='col-auto'>";
        $html[] = form_submit('submit', 'Convert', extra: [
            'class' => 'btn btn-primary',
        ]);
        $html[] = '</div>';

        $html[] = '</div>'; // row

        return implode(' ', $html);
    }
}

?>

<section>
<h1 class="section-title">Image Converter</h1>
<p class="page-lead">
    Convert any image to JPG, PNG, HEIC, BMP, GIF, WebP, or
    <?php echo count($supportedFormats); ?> other formats instantly.
</p>

<details class="help mb-3" style="padding: 8px 14px;">
    <summary style="cursor: pointer; color: var(--text-secondary); font-size: 0.875rem;">
        View all <?php echo count($supportedFormats); ?> supported formats
    </summary>
    <p style="margin-top: 8px; font-size: 0.8rem; color: var(--text-secondary);">
        <?php echo implode(', ', $supportedFormats); ?>
    </p>
</details>

<div class="form-section">
<?php
$hidden = [
    'from' => $fromFormat,
    'to' => $toFormat,
];
echo form_open_multipart('/tools/convertor/convert', hidden: $hidden);
echo _renderUploadFormInner($toFormat, fromFormat: $fromFormat, formats: $supportedFormats);
echo '</form>';
?>
</div>

<?php if ($thumbnailUri) { ?>
<div class="result">
    <p class="mb-2 fw-semibold">Conversion complete.</p>
    <?php echo sprintf("<a class='btn btn-primary btn-sm mb-3' href='%s'>Download %s</a>",
        $downloadUrl, strtoupper(pathinfo((string) $convertedFileFilename, PATHINFO_EXTENSION))); ?>
    <div>
        <?php echo sprintf("<img src='%s' class='img-fluid conversion-result-image' alt='Converted image preview' />", $thumbnailUri); ?>
    </div>
</div>
<?php } ?>
</section>

<?php echo $this->endSection(); ?>
