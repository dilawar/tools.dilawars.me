<?php
echo $this->extend('default');
echo $this->section('content');

use App\Data\ToolActionName;

$downloadUrl = $download_url ?? null;
$uploadSize = $filesize_uploaded ?? null;
$downloadSize = $filesize_result ?? null;

if (! function_exists('renderImageCompressorForm')) {
    function renderImageCompressorForm(): string
    {
        $html = [];

        $html[] = "<div class='mb-3'>";
        $html[] = "<label class='form-label fw-semibold' for='img-upload'>Image file</label>";
        $html[] = form_upload('image', extra: [
            'id'     => 'img-upload',
            'class'  => 'form-control',
            'accept' => 'image/*',
        ]);
        $html[] = '</div>';

        $html[] = form_submit('submit', 'Compress', extra: [
            'class' => 'btn btn-primary',
        ]);

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
    echo "<p class='mb-2 fw-semibold'>Your compressed image is ready.</p>";
    echo sprintf("<a href='%s' class='btn btn-primary btn-sm'>Download</a>", $downloadUrl);
    echo '<div class="mt-2">';
    echo compressionStats($downloadSize ?? -1, uploadSize: $uploadSize ?? -1);
    echo '</div>';
    echo '</div>';
}
?>
</section>

<?php echo $this->endSection(); ?>
