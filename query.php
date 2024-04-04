<?php 
include("setting.php");
require("vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

//need to get it from the front
$projectTestId = 3198;
$pass = 7;
$grade = 0;
$levels = 1;

$LowerLevelMin = 0;
$LowerLevelMax = 7;
$BasicLevelMin = 8;
$BasicLevelMax = 11;
$UpBasicLevelMin = 12;
$UpBasicLevelMax = 15;
$HighLevel = 16;

// create xlsx book
$spreadsheet = new Spreadsheet();

$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Баллы');
$sheet2 = $spreadsheet->createSheet(1);
$sheet2->setTitle('Распределение баллов');
$sheet3 = $spreadsheet->createSheet(2);
$sheet3->setTitle('Средний балл');
$sheet4 = $spreadsheet->createSheet(3);
$sheet4->setTitle('Выполнение заданий');

// get subject
$sql_subject = "SELECT
        es.Name
        FROM
        EgeSubjects es
        join ProjectTests pt on
        es.Code = pt.SubjectCode
        WHERE
        pt.Id = $projectTestId";

$stmt_subject = sqlsrv_query($conn, $sql_subject);

// get pass or fail
$row = 4;
$count = 1;
$sum_pass = 0;
$sum_fail = 0;
$sum_participants_chr = 0;
if($grade == 1){
    $sql_count = "SELECT
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

    $stmt = sqlsrv_query($conn, $sql_count);
    
    while ($row_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        while ($row_data2 = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
            $sheet1->setCellValue('A1', 'Дата:');
            $sheet1->setCellValue('A2', $row_data2['Name']);
            $sheet1->setCellValue('A3', '№');
            $sheet1->setCellValue('B3', 'Название');
            $sheet1->setCellValue('C3', 'Количество учащихся');
            $sheet1->setCellValue('D3', 'Количество участников');
            $sheet1->setCellValue('E3', 'Незачет');
            $sheet1->setCellValue('F3', 'Зачет');
        }
        $sheet1->setCellValue('A' . $row, $count);
        $sheet1->setCellValue('B' . $row, $row_data['Name']);
        $sheet1->setCellValue('C' . $row, $row_data['Количество учащихся, зарегистрированных в базе']);
        $sheet1->setCellValue('D' . $row, $row_data['Количество участников диагностики']);
        $sheet1->setCellValue('E' . $row, $row_data['Незачет']/$row_data['Количество участников диагностики']);
        $sheet1->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
        $sum_fail = $sum_fail+$row_data['Незачет'];

        $sheet1->setCellValue('F' . $row, $row_data['Зачет']/$row_data['Количество участников диагностики']);
        $sheet1->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
        $sum_pass = $sum_pass+$row_data['Зачет'];

        $sum_participants_chr = $sum_participants_chr + $row_data['Количество участников диагностики'];

        $row++;
        $count++;
    }
    sqlsrv_free_stmt($stmt);

}

if ($levels == 1){
    $row_levels = 4;
    $sql_levels = "SELECT
        a.Name,
        SUM(CASE 
        WHEN pt.ProjectTestId = $projectTestId THEN 1 ELSE 0
      END) as 'Количество учащихся, зарегистрированных в базе',
        SUM(CASE 
        WHEN pt.PrimaryMark IS NOT NULL THEN 1 ELSE 0
      END) as 'Количество участников диагностики',
        SUM(CASE 
        WHEN pt.PrimaryMark >= $LowerLevelMin AND pt.PrimaryMark <= $LowerLevelMax THEN 1 ELSE 0
      END) as 'Низкий уровень',
        SUM(CASE 
        WHEN pt.PrimaryMark >= $BasicLevelMin AND pt.PrimaryMark <= $BasicLevelMax THEN 1 ELSE 0
      END) as 'Базовый уровень',
        SUM(CASE 
        WHEN pt.PrimaryMark >= $UpBasicLevelMin AND pt.PrimaryMark <= $UpBasicLevelMax THEN 1 ELSE 0
      END) as 'Выше базового',
        SUM(CASE 
        WHEN pt.PrimaryMark >= $HighLevel THEN 1 ELSE 0
      END) as 'Высокий уровень'
    FROM
        Particips p
    LEFT JOIN ParticipTests pt ON
        p.Id = pt.ParticipId
    LEFT JOIN Schools s ON
        s.Id = p.SchoolId
    LEFT JOIN Areas a ON
        a.Code = s.AreaCode
    WHERE
        pt.ProjectTestId = $projectTestId
        AND p.SchoolId NOT IN ('0000')
        AND s.IsAlive = 1
    GROUP BY
        a.Name";

$stmt_levels = sqlsrv_query($conn, $sql_levels);
// dd($stmt_levels);

$stmt_subject = sqlsrv_query($conn, $sql_subject);
while($levels_data = sqlsrv_fetch_array($stmt_levels, SQLSRV_FETCH_ASSOC)){
    while ($levels_subject = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
        $sheet1->setCellValue('A1', 'Дата:');
        $sheet1->setCellValue('A2', $levels_subject['Name']);
        $sheet1->setCellValue('A3', '№');
        $sheet1->setCellValue('B3', 'Название');
        $sheet1->setCellValue('C3', 'Количество учащихся');
        $sheet1->setCellValue('D3', 'Количество участников');
        $sheet1->setCellValue('E3', 'Низкий уровень');
        $sheet1->setCellValue('F3', 'Базовый уровень');
        $sheet1->setCellValue('G3', 'Выше базового');
        $sheet1->setCellValue('H3', 'Высокий уровень');
    }
    $sheet1->setCellValue('A' . $row_levels, $count);
    $sheet1->setCellValue('B' . $row_levels, $levels_data['Name']);
    $sheet1->setCellValue('C' . $row_levels, $levels_data['Количество учащихся, зарегистрированных в базе']);
    $sheet1->setCellValue('D' . $row_levels, $levels_data['Количество участников диагностики']);
    $sheet1->setCellValue('E' . $row_levels, $levels_data['Низкий уровень']/$levels_data['Количество участников диагностики']);
    $sheet1->getStyle('E' . $row_levels)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sum_fail += $levels_data['Низкий уровень'];

    $sheet1->setCellValue('F' . $row_levels, $levels_data['Базовый уровень']/$levels_data['Количество участников диагностики']);
    $sheet1->getStyle('F' . $row_levels)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sum_pass += $levels_data['Базовый уровень'];

    $sheet1->setCellValue('G' . $row_levels, $levels_data['Выше базового']/$levels_data['Количество участников диагностики']);
    $sheet1->getStyle('G' . $row_levels)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);

    $sheet1->setCellValue('H' . $row_levels, $levels_data['Высокий уровень']/$levels_data['Количество участников диагностики']);
    $sheet1->getStyle('H' . $row_levels)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);

    $sum_participants_chr += $levels_data['Количество участников диагностики'];

    $row_levels++;
    $count++;

}
sqlsrv_free_stmt($stmt_levels);

}

$sql_points = "SELECT
                    pt.PrimaryMark AS 'Балл',
                    COUNT(pt.Id) as 'Кол-во участников получивших данный балл'
                    FROM
                    ParticipTests pt
                    JOIN Particips p ON
                    p.Id = pt.ParticipId
                    JOIN Schools s on
                    s.Id = p.SchoolId
                    WHERE
                    pt.ProjectTestId = $projectTestId
                    AND pt.PrimaryMark IS NOT NULL
                    AND s.AreaCode NOT IN (1000)
                    AND s.IsAlive = 1
                    GROUP BY
                    pt.PrimaryMark
                    ORDER BY
                    pt.PrimaryMark";

$stmt_points = sqlsrv_query($conn, $sql_points);

$sql_average_mark = "SELECT
                        a.Code,
                        a.Name AS 'Район',
                        AVG(pt.PrimaryMark) as 'Средний балл' 
                        FROM
                        ParticipTests pt
                        JOIN Particips p ON
                        p.Id = pt.ParticipId
                        JOIN Schools s ON
                        s.Id = p.SchoolId
                        JOIN Areas a ON
                        a.Code = s.AreaCode
                        WHERE
                        pt.ProjectTestId = $projectTestId
                        AND pt.PrimaryMark IS NOT NULL
                        AND s.AreaCode NOT IN (1000)
                        AND s.IsAlive = 1
                        GROUP BY
                        a.Code,
                        a.Name
                        ORDER BY
                        a.Code";

$stmt_average_mark = sqlsrv_query($conn, $sql_average_mark);

$sql_average_mark_chr = "SELECT
                            AVG(pt.PrimaryMark) as 'Средний балл по чечне' 
                            FROM
                            ParticipTests pt
                            JOIN Particips p ON
                            p.Id = pt.ParticipId
                            JOIN Schools s ON
                            s.Id = p.SchoolId
                            JOIN Areas a ON
                            a.Code = s.AreaCode
                            WHERE
                            pt.ProjectTestId = $projectTestId
                            AND pt.PrimaryMark IS NOT NULL
                            AND s.AreaCode NOT IN (1000)
                            AND s.IsAlive = 1";

$stmt_average_mark_chr = sqlsrv_query($conn, $sql_average_mark_chr);

$sql_completing_tasks = "select
                            rq.[Order],
                            eq.ElementNames,
                            SUM(CASE 
                            WHEN qm.AwardedMark >= 1 THEN 1
                            END) as 'Количество учащихся, ответивших на вопрос'
                            from
                            QuestionMarks qm
                            join RsurQuestions rq on
                            rq.Id = qm.QuestionId
                            join EgeQuestions eq on
                            eq.Id = rq.EgeQuestionId
                            join ParticipTests pt on
                            pt.Id = qm.ParticipTestId
                            join Particips p on
                            p.Id = pt.ParticipId
                            join Schools s on
                            s.Id = p.SchoolId
                            where
                            s.AreaCode NOT IN (1000)
                            AND pt.PrimaryMark IS NOT NULL
                            and s.IsAlive = 1
                            and pt.ProjectTestId = $projectTestId
                            group by
                            rq.[Order],
                            eq.ElementNames
                            order by
                            rq.[Order]";

$stmt_completing_tasks = sqlsrv_query($conn, $sql_completing_tasks);

if (($stmt === false) || ($stmt_subject === false) || ($stmt_points === false) || 
($stmt_average_mark === false) || ($stmt_average_mark_chr === false) || ($stmt_completing_tasks === false) ||
($stmt_levels === false)) {
    die(print_r(sqlsrv_errors(), true));
}

// Points distribution
$row2 = 4;
$stmt_subject = sqlsrv_query($conn, $sql_subject);
while ($points_distribution = sqlsrv_fetch_array($stmt_points, SQLSRV_FETCH_ASSOC)) {
    while ($row_data3 = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
        $sheet2->setCellValue('A1', 'Дата:');
        $sheet2->setCellValue('A2', $row_data3['Name']);
        $sheet2->setCellValue('A3', 'Балл');
        $sheet2->setCellValue('B3', 'Доля участников (%)');
    }
    $sheet2->setCellValue('A' . $row2, $points_distribution['Балл']);
    $sheet2->setCellValue('B' . $row2, $points_distribution['Кол-во участников получивших данный балл']/$sum_participants_chr);
    $sheet2->getStyle('B' . $row2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $row2++;
}

// Average mark
$row3 = 5;
$count_average_mark = 1;
$average_chr = 0;
$stmt_subject = sqlsrv_query($conn, $sql_subject);
while($average_marks_data = sqlsrv_fetch_array($stmt_average_mark, SQLSRV_FETCH_ASSOC)){
    while($row_data4 = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
        $sheet3->setCellValue('A1', 'Дата:');
        $sheet3->setCellValue('A2', 'Расчет доверительного интервала среднего балла');
        $sheet3->setCellValue('A3', $row_data4['Name']);
        $sheet3->setCellValue('A4' , '№ п/п');
        $sheet3->setCellValue('B4' , 'ATE');
        $sheet3->setCellValue('C4' , 'Средний балл');
        $sheet3->setCellValue('D4' , 'Чеченская Республика');
        $sheet3->setCellValue('E4' , 'Нижняя граница довер, интервала');
        $sheet3->setCellValue('F4' , 'Верхняя граница довер, интервала');

        $sheet3->setCellValue('H4' , 'Альфа');
        $sheet3->setCellValue('H5' , '0,05');
        $sheet3->setCellValue('H5' , '0,05');
        $sheet3->setCellValue('H9' , 'Граница доверительного интервала');
        $sheet3->setCellValue('H10' , '=D5-I7');

        $sheet3->setCellValue('I4' , 'Стандартное отклонение (Г)');
        $sheet3->setCellValue('I5' , '=STDEV.P(C5:C23)');
        $sheet3->setCellValue('I6' , 'Доверит.Стьюдент');
        $sheet3->setCellValue('I7' , "'=ДОВЕРИТ.СТЬЮДЕНТ(H5;I5;J5)'");
        $sheet3->setCellValue('I9' , 'Граница доверительного интервала');
        $sheet3->setCellValue('I10' , '=D5+I7');

        $sheet3->setCellValue('J4' , 'Размер');

        $sheet3->setCellValue('J5' , '19');
    }
    $sheet3->setCellValue('A' . $row3, $count_average_mark);
    $sheet3->setCellValue('B' . $row3, $average_marks_data['Район']);
    $sheet3->setCellValue('C' . $row3, $average_marks_data['Средний балл']);
    $sheet3->setCellValue('D' . $row3, '=C24');
    $sheet3->setCellValue('E' . $row3, '=H10');
    $sheet3->setCellValue('F' . $row3, '=I10');
    $count_average_mark++;
    $row3++;
}
$sheet3->setCellValue('A' . $row3, $count_average_mark);
$sheet3->setCellValue('B' . $row3, 'ЧР');
$sheet3->setCellValue('D' . $row3, '=C24');
$sheet3->setCellValue('E' . $row3, '=H10');
$sheet3->setCellValue('F' . $row3, '=I10');
while($average_mark_chr_data = sqlsrv_fetch_array($stmt_average_mark_chr, SQLSRV_FETCH_ASSOC)){
    $sheet3->setCellValue('C' . $row3, $average_mark_chr_data['Средний балл по чечне']);
}

// completing tasks

$row4 = 4;
$stmt_subject = sqlsrv_query($conn, $sql_subject);
while ($task_completing = sqlsrv_fetch_array($stmt_completing_tasks, SQLSRV_FETCH_ASSOC)) {
    while ($row_data5 = sqlsrv_fetch_array($stmt_subject, SQLSRV_FETCH_ASSOC)){
        $sheet4->setCellValue('A1', 'Дата:');
        $sheet4->setCellValue('A2', $row_data5['Name']);
        $sheet4->setCellValue('A3', '№ задания');
        $sheet4->setCellValue('B3', 'Процент выполнения');
        $sheet4->setCellValue('C3', 'Кол-во учеников, которые выполнили задание');
    }
    $sheet4->setCellValue('A' . $row4, $task_completing['Order']);
    $sheet4->setCellValue('B' . $row4, $task_completing['Количество учащихся, ответивших на вопрос']/$sum_participants_chr);
    $sheet4->getStyle('B' . $row4)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    $sheet4->setCellValue('C' . $row4, $task_completing['Количество учащихся, ответивших на вопрос']);
    $row4++;
}

sqlsrv_free_stmt($stmt_subject);
sqlsrv_free_stmt($stmt_completing_tasks);
sqlsrv_free_stmt($stmt_average_mark_chr);
sqlsrv_free_stmt($stmt_points);
sqlsrv_free_stmt($stmt_average_mark);
sqlsrv_close($conn);

$writer = new Xlsx($spreadsheet);
$writer->save('output.xlsx');
?>
