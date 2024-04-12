<?php
session_start();
include_once("setting.php");
require("query.php");
require("vendor/autoload.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.ico">
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/style/style.css">
    <title>Document</title>
</head>
<body>
    <div class="project">
        <div class="wrapper">
            <div class="container">
                <div class="project_dannie">
                    <form action="/query.php" method="post">
                        <div class="mb-3">
                            <label for="project" class="form-label">Test ID</label>
                            <input type="text" class="form-control" id="projectTestId" name="projectTestId">
                        </div>
                        <div class="mb-3">
                            <label for="project" class="form-label">Project Pass</label>
                            <input type="text" class="form-control" id="projectPass" name="projectPass">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="js/script.js"></script>
<script src="js/jquery.js"></script>
</body>
</html>