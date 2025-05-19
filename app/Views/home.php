<?php echo $this->extend('default'); ?>
<?php echo $this->section('content'); ?>

<?php 

if(! function_exists('renderToolCard')) {

    /**
     * @param array{href: string, text: string} $link
     */
    function renderToolCard(string $title, string $body = '', ?array $link = null): string 
    {
        $html = ["<div class='card h-100'>"];

        $html[] = "<div class='card-body'>";
        $html[] = "<h4 class='card-title'>$title</h4>";
        $html[] = "<p class='card-text'>$body</p>";

        if($link) {
            $html[] = "<a class='btn btn-primary stretched-link' href='" . $link['href'] . "'>" . $link['text'] . "</a>";
        }
        $html[] = "</div>";

        $html[] = "</div>";
        return implode(' ', $html);
    }
}

?>

<section>
    <div class="row mx-1 g-2">
        <!-- Compress image -->
        <div class="col-12 col-sm-6">
            <?= renderToolCard("Compress Image",
                body: 'Compress image',
                link: [
                    'href' => '/tool/compress',
                    'text' => 'Open Image Compressor',
                ]); 
            ?>

            <div id="translations" style="display: none;">
              <div data-lang="en">Compress your image file</div>
              <div data-lang="hi">अपनी छवि फ़ाइल को संकुचित करें</div>
              <div data-lang="bn">আপনার ইমেজ ফাইল সংকুচিত করুন</div>
              <div data-lang="ta">உங்கள் படக் கோப்பை சுருக்கவும்</div>
              <div data-lang="te">మీ చిత్ర ఫైల్‌ను సంకోచించండి</div>
              <div data-lang="kn">ನಿಮ್ಮ ಚಿತ್ರ ಫೈಲ್ ಅನ್ನು ಸಂಕುಚಿತಗೊಳಿಸಿ</div>
              <div data-lang="ml">നിങ്ങളുടെ ചിത്രം ഫയൽ കമ്പ്രസ് ചെയ്യുക</div>
              <div data-lang="mr">आपली प्रतिमा फाइल संकुचित करा</div>
              <div data-lang="gu">તમારી છબી ફાઇલને સંકોચો</div>
              <div data-lang="pa">ਆਪਣੀ ਚਿੱਤਰ ਫਾਈਲ ਨੂੰ ਸੰਕੁਚਿਤ ਕਰੋ</div>
            </div>
        </div>

    
        <!-- Convert one image format to another -->
        <div class="col-12 col-sm-6">
            <?= renderToolCard("Convert To Multiple Format",
                body: 'Convert your image to JPG, PNG, HEIC, BMP, GIF, and 100 other formats',
                link: [
                    'href' => '/tool/convert',
                    'text' => 'Open Image Convertor',
                ]); 
            ?>
            <div style="display: none;">
              <!-- Hindi -->
              छवि को PNG, JPG, BMP, ICON, GIF और 100 अन्य फ़ॉर्मेट्स में कनवर्ट करें।  
              <!-- Bengali -->
              ছবিটি PNG, JPG, BMP, ICON, GIF এবং আরও 100টি ফরম্যাটে রূপান্তর করুন।  
              <!-- Tamil -->
              படத்தை PNG, JPG, BMP, ICON, GIF மற்றும் பிற 100 வடிவங்களாக மாற்றவும்.  
              <!-- Telugu -->
              చిత్రాన్ని PNG, JPG, BMP, ICON, GIF మరియు మరో 100 ఫార్మాట్లకు మార్పు చేయండి.  
              <!-- Kannada -->
              ಚಿತ್ರವನ್ನು PNG, JPG, BMP, ICON, GIF ಮತ್ತು ಇನ್ನೂ 100 ಫಾರ್ಮಾಟ್‌ಗಳಿಗೆ ಪರಿವರ್ತಿಸಿ.  
              <!-- Malayalam -->
              ചിത്രത്തെ PNG, JPG, BMP, ICON, GIF എന്നിവയും മറ്റ് 100 ഫോർമാറ്റുകളും ആക്കുക.  
              <!-- Gujarati -->
              છબીને PNG, JPG, BMP, ICON, GIF અને અન્ય 100 ફોર્મેટ્સમાં રૂપાંતરિત કરો.  
              <!-- Marathi -->
              प्रतिमेचे PNG, JPG, BMP, ICON, GIF आणि इतर 100 फॉरमॅट्समध्ये रूपांतर करा.  
              <!-- Punjabi -->
              ਚਿੱਤਰ ਨੂੰ PNG, JPG, BMP, ICON, GIF ਅਤੇ ਹੋਰ 100 ਫਾਰਮੈਟਾਂ ਵਿੱਚ ਬਦਲੋ।  
              <!-- Urdu -->
              تصویر کو PNG، JPG، BMP، ICON، GIF اور 100 دیگر فارمیٹس میں تبدیل کریں۔
            </div>
        </div>

        <!-- HEIF to JPEG -->
        <div class="col-12 col-sm-6">

            <?= renderToolCard("Convert To JPEG", body : 'Convert HEIC, PNG, etc. to a JPEG', link: [
                'href' => '/tool/convert/jpeg',
                'text' => 'Convert to JPEG',
            ], ); ?>

            <div hidden>
                <p><strong>Hindi (हिन्दी):</strong> HEIC को JPEG में बदलें</p>
                <p><strong>Kannada (ಕನ್ನಡ):</strong> HEIC ಅನ್ನು JPEG ಗೆ ಪರಿವರ್ತಿಸಿ</p>
                <p><strong>Tamil (தமிழ்):</strong> HEIC-ஐ JPEG-ஆக மாற்றவும்</p>
                <p><strong>Telugu (తెలుగు):</strong> HEIC ను JPEG గా మార్చండి</p>
                <p><strong>Marathi (मराठी):</strong> HEIC चे JPEG मध्ये रूपांतर करा</p>
            </div>
        </div>
    </div>
</section>

<?php echo $this->endSection(); ?>
