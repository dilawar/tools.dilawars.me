<?php

if($images ?? []) 
{
    echo "<div class='mt-3'>";
    echo "<h4 class='text-success'>Your results are ready to download!</h4>";

    echo "<div class='row'>";
    foreach($images as $image) {
        $downloadUrl = $image->downloadUrl();
        $thumbnailUri = $image->thumbnailUri();

        echo "<div class='col-6'>";
        echo "<figure class='p-1' style='border: 1px solid gray; border-radius: 20px;'>";
        echo "<img src='$thumbnailUri' class='img-fluid conversion-result-image' />";
        echo '<figcaption class="d-flex justify-content-center">';
        echo "<a class='btn btn-link' target='_blank' href='$downloadUrl'>Download </a>";
        echo '</figcaption>';
        echo "</figure>";
        echo "</div>";
    }
    echo "</div>";

    echo "</div>";
}
?>
