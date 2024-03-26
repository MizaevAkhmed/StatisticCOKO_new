<?php 
include("setting.php");
require("vendor/autoload.php");


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// $spreadsheet = new Spreadsheet();
// $activeWorksheet = $spreadsheet->getActiveSheet();
// $activeWorksheet->setCellValue('A1', 'Hello World !');

// $writer = new Xlsx($spreadsheet);
// $writer->save('hello world.xlsx');

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
    // $activeWorksheet->getStyle('A1:F1')->getAlignment()->applyFromArray($alignment);
    // dd($activeWorksheet);


    if (($stmt === false) || ($stmt_subject === false)) {
        die(print_r(sqlsrv_errors(), true)); // Handle query execution error
    }
    

    $row = 3;
    $count = 1;
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
        $activeWorksheet->setCellValue('F' . $row, $row_data['Зачет']/$row_data['Количество участников диагностики']);
        $activeWorksheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
        $row++;
        $count++;
    }
    $activeWorksheet->setCellValue('A' . 22, $count++);
    $activeWorksheet->setCellValue('B' . 22, 'ЧР');
    $activeWorksheet->setCellValue('C' . 22, "=sum(C3:C21)");
    $activeWorksheet->setCellValue('D' . 22, "=sum(D3:D21)");
    $activeWorksheet->setCellValue('D' . 22, "=sum(D3:D21)");


    sqlsrv_free_stmt($stmt); // Free the statement resources

    sqlsrv_close($conn); // Close the connection

    $writer = new Xlsx($spreadsheet);
    $writer->save('output.xlsx');

    /*
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $objPHPExcel  = new PHPExcel();
    $objPHPExcel->getActiveSheet()->setTitle('Данные');

    // Установка заголовков столбцов
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Код');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Название');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Количество учащихся');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Количество участников');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Незачет');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Зачет');
    
    // Заполнение данными из базы данных
    $rowNumber = 2;
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNumber, $row['Code']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNumber, $row['Name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowNumber, $row['Количество учащихся, зарегистрированных в базе']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowNumber, $row['Количество участников диагностики']);
        $value = $row['Незачет'] / $row['Количество участников диагностики'] * 100;
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, $value);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $rowNumber)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

        // $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, $row['Незачет']/$row['Количество участников диагностики']*100);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowNumber, $row['Зачет']);
        $rowNumber++;
    }

    $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

    // Настройка ширины столбцов
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

    // Настройка выравнивания
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:F' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    // Настройка формата числовых значений
    $objPHPExcel->getActiveSheet()->getStyle('C2:F' . $rowNumber)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

    // Настройка границ ячеек
    $objPHPExcel->getActiveSheet()->getStyle('A1:F' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Сохранение документа
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('output.xlsx');

    echo ('output.xlsx');
}*/