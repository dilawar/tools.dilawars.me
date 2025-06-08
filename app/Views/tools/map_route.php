<?php
helper('xml');
echo $this->extend('default');
echo $this->section('content');

$resultGpx = $result ?? [];

$default = [
    'start_time' => htmlDatetimeLocal($start_time ?? 'now'),
    'end_time' => htmlDatetimeLocal($end_time ?? '+1 hour'),
];

if(! function_exists('renderBuildMyRouteForm')) {
    function renderBuildMyRouteForm(array $default = []): string 
    {
        $html[] = form_open_multipart("/tool/geo/map_route");
        $html[] = formUploadFile('geojson', "Upload geojson file", accept: '.json,.geojson');
        $html[] = formInputBootstrap('start_time', "Start Time", value: $default['start_time'] ?? '', type: 'datetime-local');
        $html[] = formInputBootstrap('end_time', "End Time", value: $default['end_time'] ?? '', type: 'datetime-local');
        $html[] = submitButton('Generate GPX', extraClass: 'form-group', divClass: 'col');
        $html[] = "</div>"; // row
        return implode(' ', $html);
    }
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- locate control plugin -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.84.2/dist/L.Control.Locate.min.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.84.2/dist/L.Control.Locate.min.js" charset="utf-8"></script>

<!-- easy button plugin -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
<script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>

<section>
    <section class='mb-2'>
        <?=  renderBuildMyRouteForm($default) ?>
    </section>
</section>

<section>
<?php
if($resultGpx) {
    $gpxFileName = $resultGpx['filename'];
    $gpxFileContent = 'data:application/xml;base64,' . base64_encode($resultGpx['xml']);
    echo "<div class='h4 mx-4 text-info'>
        Your result is ready <a class='btn btn-link h4' href='$gpxFileContent' download='$gpxFileName'>Download GPX</a>
    </h4>";
}
?>
</section>

<section>
    <h3>Map my route</h3>
    <p>
        Use the following tool to map your route and download the geojson file. Then use the form above to generate a GPX 
        file with timestamps. For more advanced needs, you can try <a href="https://gotoes.org/strava/Add_Timestamps_To_GPX.php">this tool</a>.
    </p>
</section>
<div id="map" style="height: 80vh"></div>

<script src="/assets/js/map_route.js"></script>


<?php echo $this->endSection(); ?>
