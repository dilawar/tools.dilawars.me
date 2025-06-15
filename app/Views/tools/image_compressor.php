<?php
echo $this->extend('default');
echo $this->section('content');

use App\Data\ToolActionName;

$downloadUrl = $download_url ?? null;
$uploadSize = $filesize_uploaded ?? null;
$downloadSize = $filesize_result ?? null;

if(! function_exists('renderImageCompressorForm')) 
{
    // render image compressor form.
    function renderImageCompressorForm(): string {
        $html = [];

        $html[] = "<div class='row'>";

        $html[] = '<div class="col-6">';
        $html[] = form_upload("image", extra: [
            'class' => 'form-control',
            'accept' => 'image/*',
        ]);
        $html[] = "</div>";

        $html[] = '<div class="col-2">';
        $html[] = form_submit("submit", "Compress", extra: [
            'class' => 'btn btn-primary form-control',
        ]);
        $html[] = "</div>";

        $html[] = '</div>';

        return implode(' ', $html);
    }
}

?>

<section>
    <p>
        This tool reduces the size of image by compressing it. The final image will be in JPEG
        format. This tool does not change the dimentions (width and height) of the file.
    </p>


<?php
echo form_open_multipart("tool/action/compress/" . ToolActionName::CompressImage->value);
echo renderImageCompressorForm();
echo "</form>";
?>
</section>

<section>
    <div class='result'>
<?php
if($downloadUrl) {
    echo '<h4>Your compressed image is ready. ';
    echo "<a href='$downloadUrl' class='btn btn-success'>Click Here To Download</a>";
    echo '</h4>';
    echo compressionStats($downloadSize ?? -1, uploadSize: $uploadSize ?? -1);
}

?>

    </div>
</section>

<?php echo $this->endSection(); ?>
