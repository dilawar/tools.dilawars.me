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

<p>
    This tool converts <?php echo pill($fromFormat); ?> image to <?php echo pill($toFormat); ?> format. 
</p>

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
    echo "<div class='mt-5 conversion-result'>";
    echo "<p>Your file is successfully converted.</p>";
    echo "<img src='$convertedFileUri' width='100%' />";
    echo "<br />";
    echo "<a class='btn btn-primary mt-2' 
        href='$convertedFileUri' 
        style='float: right;'
    download='$convertedFileFilename'> Download </a>";
    echo "</div>";
}
?>

<?php echo $this->endSection(); ?>
