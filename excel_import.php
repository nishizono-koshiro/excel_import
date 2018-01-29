<?php
require_once './PHPExcel/IOFactory.php';

//ファイル名
$readFile  = "data.xlsx";
//シート名
$readSheet = "students";

$objPExcel = PHPExcel_IOFactory::load($readFile);

$objWorksheet = $objPExcel->getSheetByName($readSheet);

$recordData = $objWorksheet->toArray(null,true,true,true);

$valueData = array();

$valueData[] = array(
    "Name"     => mb_convert_encoding("名前", "EUC-JP","auto"),
    "Japanese" => mb_convert_encoding("国語", "EUC-JP","auto"),
    "Math"     => mb_convert_encoding("数学", "EUC-JP","auto"),
    "English"  => mb_convert_encoding("英語", "EUC-JP","auto"),
    "Society"  => mb_convert_encoding("社会", "EUC-JP","auto"),
    "Science"  => mb_convert_encoding("理科", "EUC-JP","auto"),
    "Total"    => mb_convert_encoding("合計点", "EUC-JP","auto")
);

for($i=0;$i<=count($recordData);$i++) {
    if($i > 3) {
        $valueData[$i]["Name"]     = trim($recordData[$i]["B"] . " " . $recordData[$i]["C"]);
        $valueData[$i]["Japanese"] = trim($recordData[$i]["D"]);
        $valueData[$i]["Math"]     = trim($recordData[$i]["E"]);
        $valueData[$i]["English"]  = trim($recordData[$i]["F"]);
        $valueData[$i]["Society"]  = trim($recordData[$i]["G"]);
        $valueData[$i]["Science"]  = trim($recordData[$i]["H"]);
        $valueData[$i]["Total"]    = $valueData[$i]["Japanese"] + $valueData[$i]["Math"] + $valueData[$i]["English"] + $valueData[$i]["Society"] + $valueData[$i]["Science"];
    }
}

foreach($valueData as $data) {
    echo str_pad($data["Name"], 18);
    echo str_pad($data["Japanese"], 6);
    echo str_pad($data["Math"], 6);
    echo str_pad($data["English"], 6);
    echo str_pad($data["Society"], 6);
    echo str_pad($data["Science"], 6);
    echo str_pad($data["Total"], 8);
    echo "\n";
}

