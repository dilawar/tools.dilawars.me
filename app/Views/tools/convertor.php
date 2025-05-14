<?php

echo $this->extend('default');
echo $this->section('content');

// @phpstan-ignore variable.undefined
$fromFormat = $from;
// @phpstan-ignore variable.undefined
$toFormat = $to;

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


<?php echo $this->endSection(); ?>
