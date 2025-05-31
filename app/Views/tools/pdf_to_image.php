<?php

use App\Data\ToolActionName;

echo $this->extend('default');
echo $this->section('content');

// To format.
$toFormat = $to ?? 'jpeg';

// From format.
$fromFormat = 'pdf';

$imagesArtifacts = $image_artifacts ?? [];

/**
 * @var array<string>
 */
$supportedFormats = supportedImageFormats();

if(! function_exists('renderUploadForm')) {
    /**
     * @param array<string> $formats
     */
    function renderUploadFormInner(string $toFormat, array $formats): string 
    {
        $imageFormats = [];
        foreach($formats as $fmt) {
            $fmt = strtolower($fmt);
            $imageFormats[$fmt] = $fmt;
        }

        $html = [];
        $html[] = "<div class='row form-group mt-3 d-flex align-items-center'>";

        // Select file
        $html[] = '<div class="col-sm-5">';
        $html[] = form_input("image", type: "file", extra: [
            'class' => 'form-control',
            'accept' => '.pdf',
        ]);
        $html[] = '</div>';

        // Convert to column.
        $html[] = '<div class="col-sm-4">';
        $html[] = '<div class="input-group">';
        $html[] = "<div class='input-group-prepend'> <span class='input-group-text'>Convert To</span> </div>";
        $html[] = form_dropdown(
            'to_format',
            options: $imageFormats,
            selected: $toFormat,
            extra: [
                'id' => SELECTIZE_ID_PREFIX . '_to_format',
                'class' => 'form-control',
            ],
        );
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-sm-3">';
        $html[] = form_submit('submit', "Convert", extra: [
            'class' => 'form-control btn btn-primary',
        ]);
        $html[] = '</div>';

        $html[] = "</div>"; // ends row

        return implode(' ', $html);
    }
}

?>

<section>

<div class='h5 section-title'> Convert PDF to JPEG </div>

<?php
$hidden = [
    'from' => $fromFormat,
    'to' => $toFormat,
];
echo form_open_multipart('/tool/pdf/' . ToolActionName::PdfConvertToJpeg->value, hidden: $hidden);
echo renderUploadFormInner($toFormat, $supportedFormats);
echo '</form>';
?>
</section>

<section>
    <?= view_cell('DownloadFileCell', [
        'images' => $imagesArtifacts,
    ]) ?>
</section>

<section style="margin-top: 2ex;">
    <div style="max-width: 300px; margin: auto;">
        <?= App\Data\StatsName::table() ?>
    </div>
</section>

<?php echo $this->endSection(); ?>
