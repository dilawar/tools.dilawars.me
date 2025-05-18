<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MaxFlow Tools</title>
    <meta name="description" content="Tools made by MaxFlow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/icon.png">


    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-42NCPYMR9Z"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-42NCPYMR9Z');
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous">
    </script>


    <!-- selecize -->
    <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
            integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" 
          crossorigin="anonymous">

    <link href="<?php echo base_url(); ?>assets/app.css" rel="stylesheet">
</head>


<!-- Add hx-ext ajax-header here. See https://codeigniter.com/user_guide/general/ajax.html#htmx -->
<body ajax-header>
    <!-- Navigation bar -->
    <header>
        <nav class="navbar navbar-expand">
            <div class="container-fluid d-flex align-content-center">
                <div>
                    <a class="navbar-brand" href="/">
                        <img src="/icon.png" height="64px" />
                    </a>
                    <span class="display-6"> MaxFlow Tools </span>
                </div>
                <ul class="navbar-nav se-auto sb-2">
                </ul>
            </div>
        </nav>
    </header>

    <?php if ($msgs = getUserFlashMessage()) { ?>
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed p-1 top-0 end-0">
            <?php foreach ($msgs as $msg) { ?>
            <div class="toast show" data-bs-autohide=true data-bs-delay="10000">
                <div class="toast-header">
                    <strong class="me-auto">MaxFlow Tools</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php echo $msg; ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <!-- Generic popup for form error. -->
    <?php if (validation_errors()) { ?>
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed p-1 top-0 end-0">
            <div class="toast show" data-bs-autohide="true" data-bs-delay="10000">
                <div class="toast-header">
                    <strong class="me-auto">There is an error!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php echo validation_list_errors(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

 
    <?php echo $this->renderSection('content'); ?>

    <footer style="width: 100%; margin-top: 10vh;">

        <?php if(! isProduction()) { ?>

        <span style="font-size: small;"> 
            (version <?php echo APP_VERSION; ?>)
        </span>

        <p>Page rendered in {elapsed_time} seconds using {memory_usage} MB of memory. 
        Environment: <?php echo ENVIRONMENT; ?></p>
        <?php  } ?>

        <div class="copyrights">
            <p>&copy; <?php echo date('Y'); ?> <a href="https://maxflow.in">MaxFlow </a>
        </div>
    </footer>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" 
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
        crossorigin="anonymous">
</script>

<script
        src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
        integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer">
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" 
        crossorigin="anonymous">
</script>

<?php echo "<script> $('[id^=" . SELECTIZE_ID_PREFIX . "]').selectize(); </script>"; ?>

</body>
</html>
