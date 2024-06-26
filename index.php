<?php
session_start();
require_once("setting.php");
require_once("query.php");
require_once("vendor/autoload.php");
require_once("get_tests.php");
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
        <div class="container">
                <div class="project_dannie">
                    <div class="mb-3">
                        <label for="project" class="form-label">Название проекта</label>
                        <select class="form-select" id="select_EGE_OGE" name="select_EGE_OGE">
                            <option value="0">Выберите проект</option>
                            <option value="51">Я сдам ОГЭ</option>
                            <option value="54">Я сдам ЕГЭ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="test" class="form-label">Тесты проекта</label>
                        <select class="form-select" id="projectTestSelect" name="projectTestSelect">
                            <option value='0'>Выберите тест</option>;
                            <?php
                                echo $options;
                            ?>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                        <label class="form-check-label" for="exampleRadios1">
                            Зачет/Незачет
                        </label>
                        </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                        <label class="form-check-label" for="exampleRadios2">
                            По уровням
                        </label>
                    </div>
                    <div class="passing-score" id="passing-score" name="passing-score" style="display: none;">
                        <div class="mb-3">
                            <label for="project" class="form-label">Проходной балл</label>
                            <input type="text" class="form-control" id="projectPass" name="projectPass">
                        </div>
                    </div>
                    <div class="table-levels" id="levels" name="levels" style="display: none;">
                        <div class="table-levels-container">
                            <div class="table-cell">
                                <label for="project" class="form-label">Низкий уровень</label>
                                <section>
                                    <input type="text" class="form-control-level" id="low-level-min">
                                    <input type="text" class="form-control-level" id="low-level-max">
                                </section>
                            </div>
                            <div class="table-cell">
                                <label for="project" class="form-label">Базовый уровень</label>
                                <section>
                                    <input type="text" class="form-control-level" id="base-level-min">
                                    <input type="text" class="form-control-level" id="base-level-max">
                                </section>
                            </div>
                            <div class="table-cell">
                                <label for="project" class="form-label">Выше базового</label>
                                <section>
                                    <input type="text" class="form-control-level" id="above-base-level-min">
                                    <input type="text" class="form-control-level" id="above-base-level-max">
                                </section>
                            </div>
                            <div class="table-cell">
                                <label for="project" class="form-label">Высокий уровень</label>
                                <section>
                                    <input type="text" class="form-control-level" id="high-level-min">
                                    <!-- <input type="text" class="form-control-level" id="high-level-max"> -->
                                </section>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="generateBtn">Download</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
</body>
</html>