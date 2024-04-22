<?php
session_start();
require_once("setting.php");
require_once("query.php");
require_once("vendor/autoload.php");
// require_once("get_tests.php");
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
                    <form id="excelFile" action="/query.php" method="post">
                        <div class="mb-3">
                            <label for="project" class="form-label">Название проекта</label>
                            <select class="form-select" id="projectSelect" name="projectSelect">
                                <option>Выберите проект</option>
                                <?php
                                    // Запрос для выбора всех проектов из базы данных
                                    $sql_projects = "SELECT Id, Name FROM Projects";
                                    $result = sqlsrv_query($conn, $sql_projects);

                                    // Вывод списка проектов
                                    while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                        echo "<option value='".$row['Id']."'>".$row['Name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="test" class="form-label">Тесты проекта</label>
                            <select class="form-select" id="testSelect" name="testSelect">
                                <option selected>Выберите проект сначала</option>
                                <?php
                                    $sql_project_tests = "SELECT Id, Name FROM projectTest WHERE ProjectId = ?";
                                    $params = array($projectId);
                                    $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                    $ = sqlsrv_query($conn, $sql, $params, $options);

                                    // Вывод списка тестов
                                    $options = "<option value=''>Выберите тест</option>";
                                    while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                    $options .= "<option value='".$row['Id']."'>".$row['Name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                            <div class="mb-3">
                                <label for="project" class="form-label">Проходной балл</label>
                                <input type="text" class="form-control" id="projectPass" name="projectPass">
                            </div>
                            <button type="submit" class="btn btn-primary" id="generateBtn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
</body>
</html>