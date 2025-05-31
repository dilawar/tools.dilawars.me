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

if(! function_exists('renderPdfCompressForm')) {
    function renderPdfCompressForm(): string 
    {
        $html = [];
        $html[] = "<div class='row form-group mt-3 d-flex align-items-center'>";

        // Select file
        $html[] = '<div class="col-sm-5">';
        $html[] = form_input("image", type: "file", extra: [
            'class' => 'form-control',
            'accept' => '.pdf',
        ]);
        $html[] = '</div>';

        $html[] = '<div class="col-sm-3">';
        $html[] = form_submit('submit', "Compress", extra: [
            'class' => 'form-control btn btn-primary',
        ]);
        $html[] = '</div>';

        $html[] = "</div>"; // ends row

        return implode(' ', $html);
    }
}

?>

<section>

<div class='h5 section-title'> Compress PDF </div>

<?php
echo form_open_multipart('/tool/pdf/' . ToolActionName::PdfCompress->value);
echo renderPdfCompressForm();
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
