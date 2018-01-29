<?php 
require_once('simple_html_dom.php');

$login_url='https://premier.no1s.biz/users/login';

$data = array( 
    "email" => "micky.mouse@no1s.biz", 
    "password" => "micky", 
);
  
$cookie=tempnam(sys_get_temp_dir(),'cookie_');

//フォーム内のトークンを分析するためのHTMLを取得
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$login_url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie);
$html=curl_exec($ch);
curl_close($ch);

//HTML解析を行いトークンを取得
$dom=new DOMDocument();
@$dom->loadHTML($html);
$xpath=new DOMXPath($dom);
$node=$xpath->query('//input[@type="hidden"]');
foreach($node as $v) {
    $data[$v->getAttribute('name')]=$v->getAttribute('value');
}

//POSTデータを飛ばしログイン
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$login_url);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

$output1 = curl_exec($ch);

//2ページ目に遷移
$page2_url = "https://premier.no1s.biz/admin?page=2";
curl_setopt($ch,CURLOPT_URL,$page2_url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$output2 = curl_exec($ch);

//3ページ目に遷移
$page3_url = "https://premier.no1s.biz/admin?page=3";
curl_setopt($ch,CURLOPT_URL,$page3_url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$output3 = curl_exec($ch);

curl_close($ch);
unlink($cookie);

//スクレイピングしてデータを格納
$html= str_get_html($output1 . $output2 . $output3);
$foods = array();
for($i=0;$i<count($html->find('tr'));$i++) {
    $line = str_get_html($html->find('tr')[$i]->innertext);
    if(strpos($line,'<td>') !== false) {
        foreach($line->find('td') as $val) {
            $foods[$i][] = "$val->innertext";
        }
    }
}

//CSVファイルを出力
$f = fopen("result.csv", "w");
if ($f) {
    foreach($foods as $food){
        _fputcsv($f, $food);
    }
}
fclose($f);

//ダブルクォーテーション用にfputcsvを自作
function _fputcsv($fp, $fields) {
    $tmp = array();
    foreach ($fields as $value) {
        $value = str_replace('"', '""', $value);
        $tmp[]= '"'.$value.'"';
    }
    $str = implode(',', $tmp);
    $str .= "\n";
    fputs($fp, $str);
}
