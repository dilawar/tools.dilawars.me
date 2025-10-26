<?php
helper('xml');
echo $this->extend('default');
echo $this->section('content');

$resultGpx = $result ?? [];

$default = [
    'start_time' => htmlDatetimeLocal($start_time ?? 'now'),
    'end_time' => htmlDatetimeLocal($end_time ?? '+1 hour'),
];

if (! function_exists('renderBuildMyRouteForm')) {
    function renderBuildMyRouteForm(array $default = []): string
    {
        $html[] = form_open('/tool/geo/map_route');
        $html[] = form_input('geojson', value: '', extra: [
            'hidden' => true,
            'id' => 'map_route_geojson_data',
        ]);

        $html[] = formInputBootstrap('start_time', 'Start Time', value: $default['start_time'] ?? '', type: 'datetime-local');
        $html[] = formInputBootstrap('end_time', 'End Time', value: $default['end_time'] ?? '', type: 'datetime-local');

        $html[] = "<div class='row justify-content-center'>";
        $html[] = "<div class='btn btn-primary mt-1' onclick='downloadGpx()'>Download GPX</div>";
        $html[] = '</div>';

        $html[] = form_submit('submit', extra: [
            'hidden' => true,
            'id' => 'map_route_submit',
        ]);
        $html[] = form_close();
        $html[] = '</div>'; // row

        return implode(' ', $html);
    }
}
?>

<link rel="stylesheet" 
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin="">
</script>

<!-- locate control plugin -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.84.2/dist/L.Control.Locate.min.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.84.2/dist/L.Control.Locate.min.js" charset="utf-8"></script>

<!-- easy button plugin -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
<script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>


<!-- Form -->
<section>
    <div class='h3'>Map my running route</div>
    <p>
        Map your route, add start time and end time of the run and download GPX file to 
        upload it on Strava or other services.
        <small> For more advanced needs, try
            <?php echo a('https://gotoes.org/strava/Add_Timestamps_To_GPX.php', 'this amazing tool'); ?>.
        </small>
    </p>
</section>
<div id="map" style="height: 500px"></div>

<script src="/assets/js/map_route.js"></script>
<!-- Input form -->
<section>
    <section class='mb-2'>
        <?php echo  renderBuildMyRouteForm($default); ?>
    </section>
</section>



<section>
<?php
if ($resultGpx) {
    $gpxFileName = $resultGpx['filename'];
    $gpxFileContent = 'data:application/geo+json;base64,'.base64_encode((string) $resultGpx['xml']);

    echo "<div class='h4 mx-4 text-info'>
        Your result is ready <a class='btn btn-link h4' href='{$gpxFileContent}' download='{$gpxFileName}'>Download GPX</a>
    </h4>";
}
?>
</section>

<script lang="js">
/*
    * When user click on button 'Download GPX' this method is called. 
    *
    * We fill values in the form and 
 */
function downloadGpx() {
    const gpx = routeDataAsJsonStr();
    // set data in form.
    const elem = document.getElementById("map_route_geojson_data");
    elem.value = gpx;

    console.debug("sending geojson:\n", elem.value);
    const formButton = document.getElementById('map_route_submit');
    formButton.click();
}

</script>

<?php echo $this->endSection(); ?>
