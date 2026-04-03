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

<section>
<h1 class="section-title">LWN Article Alerts</h1>
<p class="page-lead">
    Get notified when a recent LWN.net article becomes publicly available.
</p>

<div class='readable'>
    <p>
        When a paywalled LWN article becomes open, we post a notification to
        <?php echo a(
            'https://groups.google.com/g/maxflow-lwn-notification',
            'this Google Group',
        ); ?>.
        Join the group to receive email alerts.
    </p>

    <!--
    you get an email notification.
    <?php echo renderLwnSubscriptionForm(); ?>
    -->

    
</div>
</section>

<?php echo $this->endSection(); ?>
