<?php
require_once('vendor/autoload.php');

// Подключение к базе данных
require_once('setting.php');

// Получение ID проекта из GET параметров
$projectId = $_GET['project_id'];

// Запрос для выбора всех тестов для выбранного проекта
$sql_project_id = "SELECT Id, Name FROM ProjectTests WHERE projectId = $projectId";
$result = sqlsrv_query($conn, $sql_project_id);

// Вывод списка тестов
$options = "<option value=''>Выберите тест</option>";
while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
  $options .= "<option value='".$row['id']."'>".$row['name']."</option>";
}
?>
