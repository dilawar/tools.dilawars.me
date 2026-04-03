<?php
echo $this->extend('default');
echo $this->section('content');

use App\Data\ToolActionName;

$downloadUrl = $download_url ?? null;
$uploadSize = $filesize_uploaded ?? null;
$downloadSize = $filesize_result ?? null;

if (! function_exists('renderImageCompressorForm')) {
    // render image compressor form.
    function renderImageCompressorForm(): string
    {
        $html = [];
        $html[] = "<div class='row'>";
        $html[] = '<div class="col-6">';
        $html[] = form_upload('image', extra: [
            'class' => 'form-control',
            'accept' => 'image/*',
        ]);
        $html[] = '</div>';

        $html[] = '<div class="col-2">';
        $html[] = form_submit('submit', 'Compress', extra: [
            'class' => 'btn btn-primary form-control',
        ]);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(' ', $html);
    }
}

?>

<section>
    <h1 class="section-title">Image Compressor</h1>
    <p class="page-lead">
        Compress any image to a smaller JPEG without changing its dimensions.
        Useful for reducing file sizes before uploading or sharing.
    </p>

    <div class="form-section">
<?php
echo form_open_multipart('tool/action/compress/'.ToolActionName::CompressImage->value);
echo renderImageCompressorForm();
echo '</form>';
?>
    </div>

</section>

<section>
<?php
if ($downloadUrl) {
    echo "<div class='result'>";
    echo '<h4>Your compressed image is ready. ';
    echo sprintf("<a href='%s' class='btn btn-success'>Click Here To Download</a>", $downloadUrl);
    echo '</h4>';
    echo compressionStats($downloadSize ?? -1, uploadSize: $uploadSize ?? -1);
    echo '</div>';
}
?>
</section>

<?php echo $this->endSection(); ?>
