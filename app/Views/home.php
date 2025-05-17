<?php echo $this->extend('default'); ?>
<?php echo $this->section('content'); ?>

<?php 

if(! function_exists('renderToolCard')) {

    function renderToolCard(string $title, string $body = '', ?array $link = null): string 
    {
        $html = ["<div class='card h-100'>"];

        $html[] = "<div class='card-body'>";
        $html[] = "<h4 class='card-title'>$title</h4>";
        $html[] = "<p class='card-text'>$body</p>";

        if($link) {
            $html[] = "<a class='btn btn-primary' href='" . $link['href'] . "'>" . $link['text'] . "</a>";
        }
        $html[] = "</div>";

        $html[] = "<div hidden>
                    <p><strong>Hindi (हिन्दी):</strong> HEIC को JPEG में बदलें</p>
                    <p><strong>Kannada (ಕನ್ನಡ):</strong> HEIC ಅನ್ನು JPEG ಗೆ ಪರಿವರ್ತಿಸಿ</p>
                    <p><strong>Tamil (தமிழ்):</strong> HEIC-ஐ JPEG-ஆக மாற்றவும்</p>
                    <p><strong>Telugu (తెలుగు):</strong> HEIC ను JPEG గా మార్చండి</p>
                    <p><strong>Marathi (मराठी):</strong> HEIC चे JPEG मध्ये रूपांतर करा</p>
                </div>";

        $html[] = "</div>";
        return implode(' ', $html);
    }
}

?>

<section>
    <div class="row mx-1">
        <!--
        <div class="col-4">
            <div class="card mx-1">
                <div class="card-body">
                    <h5 class="card-title">Compress Image</h5>
                    <p class="card-text">Yet another tools to compress images.</p>
                    <a href="/tool/compress/image" class="card-link">Open Image Compressor</a>
                </div>
            </div>
        </div>
        -->

        <!-- HEIF to JPEG -->
        <div class="col-12 col-sm-6">

        <?= renderToolCard("HEIC To JPEG", body : 'Convert HEIC image to a JPEG', link: [
            'href' => '/tool/convert/heic/jpeg',
            'text' => 'HEIC to JPEG',
        ], ); ?>
        </div>
            
        <!-- Convert one image format to another -->
        <div class="col-12 col-sm-6">
        <?= renderToolCard("Change Image Type",
            body: 'Convert image in PNG, JPG, SVG, HEIC etc into other formats',
            link: [
                'href' => '/tool/convert',
                'text' => 'Open Image Convertor',
            ]); 
        ?>
        </div>
    </div>
</section>

<?php echo $this->endSection(); ?>
