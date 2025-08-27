<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

if ($images ?? []) {
    echo "<div class='mt-3 result'>";
    echo "<h4 class='text-success'>Your results are ready.</h4>";

    echo "<div class='row'>";
    foreach ($images as $image) {
        $downloadUrl = $image->downloadUrl();
        $thumbnailUri = $image->thumbnailUri();

        echo "<div class='col-6'>";
        echo "<figure class='p-1'>";
        if (! str_contains((string) $thumbnailUri, 'application/pdf')) {
            echo "<img style='width:90%; margin: auto;' src='$thumbnailUri' class='img-fluid conversion-result-image' />";
        }

        echo '<figcaption class="d-flex justify-content-center">';
        echo "<a class='btn btn-link' target='_blank' href='$downloadUrl'>Download</a>";
        echo '</figcaption>';
        echo '</figure>';
        echo '</div>';
    }
    echo '</div>';

    echo '</div>';
}
