<?php

echo $this->extend('default');
echo $this->section('content');

// @phpstan-ignore variable.undefined
$toFormat = $to;

$convertedFileUri = $converted_file_uri ?? null;
$convertedFileFilename = $converted_file_filename ?? '';

/**
 * @var array<string>
 */
$supportedFormats = supportedImageFormats();

if(! function_exists('renderUploadForm')) {

    /**
     * @param array<string> $formats
     */
    function renderUploadFormInner(string $toFormat, array $formats): string {

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
            'accept' => "image/*",
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
<div class='h5'>
    Welcome to image conversion tool.
</div>

<details style="margin:10px;">
    <summary> Total <?= count($supportedFormats) ?> formats are supported. </summary>
    <?= implode(', ', $supportedFormats) ?>.
</details>

<?php
echo form_open_multipart('/tools/convertor/convert');
echo renderUploadFormInner($toFormat, $supportedFormats);
echo '</form>';
?>

<!-- result -->
<?php
if($convertedFileUri) 
{
    echo "<div class='mt-3'>";
    echo "<p>Your file has been successfully converted. It's new name is <tt>$convertedFileFilename</tt>.
        Following is a preview of the result. The quality and size of the downloaded image may vary.
    </p>";
    echo "<a class='btn btn-primary mt-1 mb-1' 
        href='$convertedFileUri' 
        download='$convertedFileFilename'> Click Here To Download </a>";

    echo "<div>";
    echo "<img src='$convertedFileUri' class='img-fluid conversion-result-image' />";
    echo "<br />";
    echo "</div>";

    echo "</div>";
}
?>

</section>

<?php echo $this->endSection(); ?>
