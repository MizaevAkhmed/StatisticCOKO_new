<?php 
include("setting.php");
include("./include/Classes/PHPExcel.php");

if ($_GET['x']) {
    $projectTestId = 3280;
    $pass = 7;

    $sql = "SELECT
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

    $stmt = sqlsrv_query($conn, $sql);
    
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
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, $row['Незачет']);
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
}