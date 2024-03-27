<?php 
include("setting.php");
require("vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$projectTestId = 3198;
$pass = 7;

$sql_levels = "SELECT
        a.Code,
        a.Name, 
        SUM(CASE WHEN pt.ProjectTestId = $projectTestId THEN 1 ELSE 0 END) as 'Количество учащихся, зарегистрированных в базе',
        SUM(CASE WHEN pt.PrimaryMark IS NOT NULL THEN 1 ELSE 0 END) as 'Количество участников диагностики',
        SUM(CASE WHEN pt.PrimaryMark <= $pass THEN 1 ELSE 0 END) as 'Незачет',
        SUM(CASE WHEN pt.PrimaryMark > $pass THEN 1 ELSE 0 END) as 'Зачет'
        FROM
        Particips p
        LEFT JOIN ParticipTests pt ON p.Id = pt.ParticipId
        LEFT JOIN Schools s ON s.Id = p.SchoolId
        LEFT JOIN Areas a ON a.Code = s.AreaCode
        WHERE pt.ProjectTestId = $projectTestId
        AND p.SchoolId NOT IN ('0000')
        AND s.IsAlive = 1
        GROUP BY a.Code, a.Name 
        ORDER BY a.Code, a.Name";

$stmt = sqlsrv_query($conn, $sql_levels);

$sql_subject = "SELECT
        es.Name
        FROM
        EgeSubjects es
        join ProjectTests pt on
        es.Code = pt.SubjectCode
        WHERE
        pt.Id = $projectTestId";

$stmt_subject = sqlsrv_query($conn, $sql_subject);

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();

$activeWorksheet->setCellValue('A1', 'Дата');
$activeWorksheet->setCellValue('A3', 'Код');
$activeWorksheet->setCellValue('A3', 'Код');
$activeWorksheet->setCellValue('B3', 'Название');
$activeWorksheet->setCellValue('C3', 'Количество учащихся');
$activeWorksheet->setCellValue('D3', 'Количество участников');
$activeWorksheet->setCellValue('E3', 'Незачет');
$activeWorksheet->setCellValue('F3', 'Зачет');

$alignment = new Alignment();
$alignment->setHorizontal(Alignment::HORIZONTAL_CENTER);

if (($stmt === false) || ($stmt_subject === false)) {
    die(print_r(sqlsrv_errors(), true)); // Handle query execution error
}
    
$row = 3;
$count = 1;
$sum_pass = 0;
$sum_fail = 0;
while ($row_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    while ($row_data2 = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
        $activeWorksheet->setCellValue('A' . 1, 'Дата');
        $activeWorksheet->setCellValue('A' . 2, $row_data2['Name']);
    }
    $activeWorksheet->setCellValue('A' . $row, $count);
    $activeWorksheet->setCellValue('B' . $row, $row_data['Name']);
    $activeWorksheet->setCellValue('C' . $row, $row_data['Количество учащихся, зарегистрированных в базе']);
    $activeWorksheet->setCellValue('D' . $row, $row_data['Количество участников диагностики']);
    $activeWorksheet->setCellValue('E' . $row, $row_data['Незачет']/$row_data['Количество участников диагностики']);
    $activeWorksheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sum_fail = $sum_fail+$row_data['Незачет'];

    $activeWorksheet->setCellValue('F' . $row, $row_data['Зачет']/$row_data['Количество участников диагностики']);
    $activeWorksheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sum_pass = $sum_pass+$row_data['Зачет'];

    $row++;
    $count++;
}
$activeWorksheet->setCellValue('A' . 22, $count++);
$activeWorksheet->setCellValue('B' . 22, 'ЧР');
$activeWorksheet->setCellValue('C' . 22, "=sum(C3:C21)");
$activeWorksheet->setCellValue('D' . 22, "=sum(D3:D21)");
$activeWorksheet->setCellValue('E' . 22, "=$sum_fail/sum(D3:D21)");
$activeWorksheet->getStyle('E' . 22)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
$activeWorksheet->setCellValue('F' . 22, "=$sum_pass/sum(D3:D21)");
$activeWorksheet->getStyle('F' . 22)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);

$newSheet = $spreadsheet->createSheet(1);
$newSheet->setTitle('Распределение баллов');
$spreadsheet->getSheet(1);

$row = 3;
$count = 1;
$sum_pass = 0;
$sum_fail = 0;
while ($row_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    while ($row_data2 = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
        $newSheet->setCellValue('A' . 1, 'Дата');
        $newSheet->setCellValue('A' . 2, $row_data2['Name']);
    }
    $newSheet->setCellValue('A' . $row, $count);
    $newSheet->setCellValue('B' . $row, $row_data['Name']);
    $newSheet->setCellValue('C' . $row, $row_data['Количество учащихся, зарегистрированных в базе']);
    $newSheet->setCellValue('D' . $row, $row_data['Количество участников диагностики']);
    $newSheet->setCellValue('E' . $row, $row_data['Незачет']/$row_data['Количество участников диагностики']);
    $newSheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sum_fail = $sum_fail+$row_data['Незачет'];

    $newSheet->setCellValue('F' . $row, $row_data['Зачет']/$row_data['Количество участников диагностики']);
    $newSheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sum_pass = $sum_pass+$row_data['Зачет'];

    $row++;
    $count++;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

$writer = new Xlsx($spreadsheet);
$writer->save('output.xlsx');