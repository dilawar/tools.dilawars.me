<?php

echo $this->extend('default');
echo $this->section('content');

// @phpstan-ignore variable.undefined
$fromFormat = $from;
// @phpstan-ignore variable.undefined
$toFormat = $to;

$convertedFileUri = $converted_file_uri ?? null;
$convertedFileFilename = $converted_file_filename ?? '';

if(! function_exists('renderUploadForm')) {
    function renderUploadFormInnert(string $fromFormat): string {
        $html = [];
        $html[] = "<div class='row form-group'>";
        $html[] = '<div class="col-6">';
        $html[] = form_input("image", type: "file", extra: [
            'class' => 'form-control',
            'accept' => ".$fromFormat",
        ]);
        $html[] = '</div>';
        $html[] = '<div class="col-3">';
        $html[] = form_submit('submit', "Convert", extra: [
            'class' => 'form-control btn btn-primary',
        ]);
        $html[] = '</div>';
        $html[] = "</div>";

        return implode(' ', $html);
    }
}

?>

<h3 class="section-title">Converting <?php echo $fromFormat; ?> to <?php echo $toFormat; ?></h3>

<?php
echo form_open_multipart('/tools/convertor/convert', hidden: [
    'from_format' => $fromFormat,
    'to_format' => $toFormat,
]);
echo renderUploadFormInnert($fromFormat);
echo '</form>';
?>

<!-- result -->
<?php
if($convertedFileUri) 
{
    echo "<div class='mt-5' style='max-width:600px; margin: auto;'>";
    echo "<img src='$convertedFileUri' width='100%' />";
    echo "<br />";
    echo "<a class='btn btn-info mt-1' 
        href='$convertedFileUri' 
        style='float: right;'
    download='$convertedFileFilename'> Download </a>";
    echo "</div>";
}
?>

<?php echo $this->endSection(); ?>
