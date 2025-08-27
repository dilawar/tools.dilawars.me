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
        foreach ($formats as $fmt) {
            $fmt = strtolower($fmt);
            $imageFormats[$fmt] = $fmt;
        }

        $html = [];
        $html[] = "<div class='row form-group mt-3 d-flex align-items-center'>";

        $accept = 'image/*';
        if ('' !== $toFormat && '0' !== $toFormat) {
            $accept = ".$fromFormat";
        }

        // Select file
        $html[] = '<div class="col-sm-5">';
        $html[] = form_input('image', type: 'file', extra: [
            'class' => 'form-control',
            'accept' => $accept,
        ]);
        $html[] = '</div>';

        // Convert to column.
        $html[] = '<div class="col-sm-4">';
        $html[] = '<span> Convert To </span>';
        $html[] = form_dropdown(
            'to_format',
            options: $imageFormats,
            selected: $toFormat,
            extra: [
                'id' => SELECTIZE_ID_PREFIX.'_to_format',
                'class' => 'form-control col',
            ],
        );
        $html[] = '</div>';

        $html[] = '<div class="col-sm-3">';
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
<div class='h3 section-title'>
    Image conversion Tool
</div>

<details style="margin:10px;">
    <summary> Total <?php echo count($supportedFormats); ?> formats are supported. </summary>
    <?php echo implode(', ', $supportedFormats); ?>.
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
    echo "<a class='btn btn-primary mt-1 mb-1' target='_blank' href='$downloadUrl'> Click To Download </a>";

    echo '<p>Following is a preview of your result. Some result may not have a visible preview.</p>';
    echo '<div>';
    echo "<img src='$thumbnailUri' class='img-fluid conversion-result-image' />";
    echo '<br />';
    echo '</div>';

    echo '</div>';
}
?>
</section>

<?php echo $this->endSection(); ?>
