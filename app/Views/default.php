<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> <?php echo $page_title ?? 'PHP Tools: QR, Image Converter, Image Compression, PDF Converter, OCR'; ?> </title>
    <meta name="description" content="Image convertor, image compressor and other tools">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/icon.jpg">


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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" 
          rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" 
          crossorigin="anonymous">

    <!-- icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@3.0.0/dist/iconify-icon.min.js"></script>

    <link href="<?php echo base_url(); ?>assets/app.css" rel="stylesheet">
</head>


<!-- Add hx-ext ajax-header here. See https://codeigniter.com/user_guide/general/ajax.html#htmx -->
<body>
    <!-- Navigation bar -->
    <header>
        <nav class="navbar flex flex-row justify-content-end">
            <div class="col-1">
                <a class="navbar-brand" href="/">
                    <img src="/icon.jpg" height="64px" />
                </a>
            </div>
            <div class='col'></div>

            <?php if (auth()->loggedIn()) { ?>
            <span class="col col-sm-3 col-md-3 text-underline">
                <small> Hi <?php echo auth()->user()?->getEmail(); ?> </small>
            </span>
            <a class="btn btn-info btn-sm col-3 col-sm-2 col-md-1 mx-1" href="/logout">Logout</a>
            <?php } else { ?>
            <a class="btn btn-link col-3 col-sm-2 col-md-1 mx-1" href="/login">Login</a>
            <?php } ?>
        </nav>
    </header>

    <?php if ($msgs = getUserFlashMessage()) { ?>
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed p-1 top-0 end-0">
            <?php foreach ($msgs as $msg) { ?>
            <div class="toast show" data-bs-autohide=true data-bs-delay="10000">
                <div class="toast-header">
                    <strong class="me-auto">Image Tools</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close">
                    </button>
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

    <footer style="max-width: 500px; margin: auto; margin-top: 10vh;">
        <section style="margin-top: 2ex;">
            <?php echo App\Data\StatsName::table(); ?>
        </section>

        <?php if (! isProduction()) { ?>
        <p>Page rendered in {elapsed_time} seconds using {memory_usage} MB of memory. 
        Environment: <?php echo ENVIRONMENT; ?></p>
        <?php  } ?>

        <div class="copyrights">
            Version <?php echo APP_VERSION; ?>
            <p>&copy; <?php echo Carbon\Carbon::now()->format('Y'); ?> <a href="https://dilawars.me">Dilawar Singh</a>
        </div>
    </footer>


<!-- Optional JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" 
        crossorigin="anonymous">
</script>

<?php echo "<script> $('[id^=".SELECTIZE_ID_PREFIX."]').select2(); </script>"; ?>

</body>
</html>
