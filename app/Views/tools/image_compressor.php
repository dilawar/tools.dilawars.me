<?php
echo $this->extend('default');
echo $this->section('content');

use App\Data\ToolActionName;

$downloadUrl = $download_url[1] ?? null;
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
<?php
if($downloadUrl) {
    $perc = floatval($downloadSize) / floatval($uploadSize) * 100.0;
    $xTimes = number_format(100.0 / $perc, 1);

    echo '<h4>Your compressed image is ready. ';
    echo "<a href='$downloadUrl' class='btn btn-success'>Click Here To Download</a>";
    echo '</h4>';

    echo "Uploaded file (" . showFilesize($uploadSize ?? -1) . ") is reduced to <strong>" . showFilesize($downloadSize ?? -1) . "</strong>.";
    echo " Compression ratio <strong>" . number_format(100.0 / $perc, 1) . '</strong>.';
    echo '</p>';
}

?>
</section>

<section style="margin-top: 2ex;">
    <div style="max-width: 300px; margin: auto;">
        <?= App\Data\StatsName::table() ?>
    </div>
</section>


<?php echo $this->endSection(); ?>
