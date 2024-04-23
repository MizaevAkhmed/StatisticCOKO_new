<?php
require_once('vendor/autoload.php');

// Подключение к базе данных
require_once('setting.php');

// Получение ID проекта из GET параметров

if(isset($_GET['ege_oge'])){
  $projectId = $_GET['ege_oge'];
  echo($projectId);
}

$sql_select_EGE_OGE = "SELECT pt.Id, pt.Comment FROM ProjectTests pt WHERE ProjectId = $projectId";

// Подготовленный запрос для выбора тестов для выбранного проекта
$result = sqlsrv_query($conn, $sql_select_EGE_OGE);

// Вывод списка тестов
while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
  echo "<option value='".$row['Id']."'>".$row['Comment']."</option>";
}
// echo $options;
?>
