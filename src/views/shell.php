<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $this->title; ?> - SlabPHP</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="This is the default page for a SlabPHP installation." />
        <meta name="robots" content="noydir,noodp">

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/slab/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/images/slab/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/slab/favicon-16x16.png">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <style type="text/css">
            .navbar { background-color: #cdcdcd; }
            .body { min-height: 400px; }
            footer { margin: 40px 0 0 0; border-top: 1px solid #efefef; font-size: 0.8em; padding: 10px 0 0 0; text-align: center; }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-static-top navbar-expand-md">
            <a class="navbar-brand" href="/">
                <img src="/images/slab/slabphp-text.png" alt="SlabPHP Logo" title="Home" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbars" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                <i class="material-icons">reorder</i>
            </button>

            <div class="collapse navbar-collapse" id="navbars">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.salernolabs.com/">Salerno Labs LLC</a>
                    </li>
                </ul>
                <div class="navbar-right header-right">
                    <a target="_blank" href="https://twitter.com/salernolabs">
                        <img src="/images/slab/twitter-m.png" alt="Follow us on twitter @salernolabs" title="Follow us on twitter @salernolabs" class="icon" />
                    </a>
                    <a target="_blank" href="https://www.facebook.com/salernolabs">
                        <img src="/images/slab/facebook-m.png" alt="Like us on facebook!" title="Like us on facebook!" class="icon" />
                    </a>
                </div>
            </div>
        </nav>

        <div class="body">
            <?php echo $this->load($this->subTemplate, $this); ?>
        </div>

        <footer class="footer">
            <div class="container">
                <p>
                    <a href="/">Home</a>
                </p>
                <p>
                    <a href="https://www.salernolabs.com/">
                        <img src="/images/slab/salerno-labs.png" alt="Salerno Labs LLC Logo" />
                    </a><br />
                    &copy; 2018 <a href="https://www.salernolabs.com/">Salerno Labs LLC</a> All Rights Reserved
                </p>
                <p>SlabPHP is a Salerno Labs LLC Formula.</p>
            </div>
        </footer>
        <?php $this->load('modules/debug.php', $this); ?>
    </body>
</html>
