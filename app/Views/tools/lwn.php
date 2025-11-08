<?php

echo $this->extend('default');
echo $this->section('content');

if (! function_exists('renderLwnSubscriptionForm')) {
    function renderLwnSubscriptionForm(): string
    {
        $html = [];
        $html[] = "<div class='mt-4'>";
        $html[] = form_open('/tools/subscription/handleLwn');
        $html[] = form_input('email', type: 'email', extra: [
            'placeholder' => 'Your email',
        ]);
        $html[] = form_submit('submit', 'Subscribe');
        $html[] = form_close();
        $html[] = '</div>';

        return implode('', $html);
    }
}

?>

<div class="h3 section-title">LWS Subscriber</div>

<div class='readable'>

    <div class='mt-1'>
        When a LWN article becomes open, we post notification to
        <?php echo a(
            'https://groups.google.com/g/maxflow-lwn-notification',
            'this google group',
        ); ?>.
    </div>

    <!--
    you get an email notification.
    <?php echo renderLwnSubscriptionForm(); ?>
    -->

    
</div>

<?php echo $this->endSection(); ?>
