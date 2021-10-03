<?php
require_once realpath("vendor/autoload.php");
use \VetTix\Service;
$token = $_COOKIE['token'];
$eventId = $_GET["eventId"] ?? "";
$numberOfSeats = $_GET["numberOfSeats"] ?? "2";
$eventDetails = service::getEventDetails($token, $eventId);
$inventory = service::getAdjacentSeats($token, $eventId, $numberOfSeats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>VetTix Demo App</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark navbar-expand-sm fixed-top">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/VetTix/index.php">
            <img height="52" width="75"
                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEsAAAA0CAYAAADCOsX+AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpGNEIzNTFCMDE4ODVFMjExQTQxNTk2OENBQzU4Q0YwNyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4QjUwNjRFOTg1NDQxMUUzQkVBNUFGNzhDMzBDQkFFMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4QjUwNjRFODg1NDQxMUUzQkVBNUFGNzhDMzBDQkFFMSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpGOUIzNTFCMDE4ODVFMjExQTQxNTk2OENBQzU4Q0YwNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpGNEIzNTFCMDE4ODVFMjExQTQxNTk2OENBQzU4Q0YwNyIvPiA8L3JkZjpEZXNjcmlwdGlv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"/>
            Demo App
        </a>
        <div class="collapse navbar-collapse" id="Navbar">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="/VetTix/search.php"><span class="fa fa-search fa-lg"></span> Search</a></li>

                <li class="nav-item active"><a class="nav-link" href="/VetTix/events.php"><span class="fa fa-ticket fa-lg"></span>
                        Events</a></li>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="text-center">
                    <img class="card-img-top" style="max-width: 350px;" src="<?php echo $eventDetails->imageURL ?>"
                         alt="<?php echo $eventDetails->title ?>">
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="card-title">
                            <?php echo $eventDetails->title ?>

                        </h2>
                        <h5 class="card-title">
                            <?php echo "(" . $numberOfSeats . " seats together)" ?>

                        </h5>
                    </div>
                    <div class="row">
                        <p class="card-text">
                            <?php
                            foreach ($inventory as $ticket) {
                                echo '<div class="col-sm-3">';
                                echo '<div class="card text-white bg-secondary mb-3">';
                                echo '<div class="card-header">Section: ' . $ticket->section . '</div>';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">Row: ' . $ticket->row . '</h5>';
                                echo '<p class="card-text">Seat: ' . $ticket->seat . '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- built files will be auto injected -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
</body>

</html>
