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


        $accept = 'image/*';
        if ('' !== $toFormat && '0' !== $toFormat) {
            $accept = '.'.$fromFormat;
        }

        // Select file
        $html = [];
        $html[] = "<div class='row form-group mt-3 d-flex align-items-center'>";
        $html[] = '<div class="col-12 col-sm-5">';
        $html[] = form_input('image', type: 'file', extra: [
            'class' => 'form-control',
            'accept' => $accept,
        ]);
        $html[] = '</div>';

        // Convert to column.
        $html[] = '<div class="col-6 col-sm-2"> Convert To </div>';
        $html[] = '<div class="col-6 col-sm-2">';
        $html[] = form_dropdown(
            'to_format',
            options: $imageFormats,
            selected: $toFormat,
            extra: [
                'id' => SELECTIZE_ID_PREFIX.'_to_format',
                'class' => 'form-control',
            ],
        );
        $html[] = '</div>';

        $html[] = '<div class="col-12 col-sm-3">';
        $html[] = form_submit('submit', 'Convert', extra: [
            'class' => 'form-control btn btn-primary',
        ]);
        $html[] = '</div>';

        $html[] = '</div>'; // ends row

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

<?php
$hidden = [
    'from' => $fromFormat,
    'to' => $toFormat,
];
echo form_open_multipart('/tools/convertor/convert', hidden: $hidden);
echo _renderUploadFormInner($toFormat, fromFormat: $fromFormat, formats: $supportedFormats);
echo '</form>';
?>

<!-- result -->
<?php
if ($thumbnailUri) {
    echo "<div class='mt-3 result'>";
    echo sprintf("<a class='btn btn-primary mt-1 mb-1' target='_blank' href='%s'> Click To Download </a>", $downloadUrl);

    echo '<p>Following is a preview of your result. Some result may not have a visible preview.</p>';
    echo '<div>';
    echo sprintf("<img src='%s' class='img-fluid conversion-result-image' />", $thumbnailUri);
    echo '<br />';
    echo '</div>';

    echo '</div>';
}
?>
</section>

<?php echo $this->endSection(); ?>
