<?php
$base = 'http://localhost:8000';
$jar = __DIR__.'/.cookie_debug.txt'; @unlink($jar);
function req($method,$url,$jar,$data=null,$headers=[],$timeout=120){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $jar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $jar);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($method==='POST') curl_setopt($ch, CURLOPT_POST, true);
    if ($data!==null) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    if ($res===false) { echo "CURL ERROR: ".curl_error($ch)."\n"; curl_close($ch); return false; }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($res,0,$header_len);
    $body = substr($res,$header_len);
    curl_close($ch);
    echo "STATUS: $status\n";
    echo "---HEADERS---\n". $header ."\n";
    echo "---BODY (first 1000 chars)---\n".substr($body,0,1000)."\n";
    return true;
}
// get csrf
req('GET',$base.'/sanctum/csrf-cookie',$jar,null,[],30);
// now register teacher
$fields = [
 'name'=>'Debug Teacher '.time(),
 'email'=>'debug_teacher_'.time().'@example.test',
 'phone'=>'123456',
 'role_slug'=>'teacher',
 'password'=>'secret123',
 'password_confirmation'=>'secret123',
 'terms'=>'1',
];
$post = http_build_query($fields);
$xsrf = null;
$lines = file($jar,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
foreach($lines as $ln){ if($ln[0]==='#') continue; $p = preg_split('/\s+/',$ln); if(count($p)>=7){ if($p[5]==='XSRF-TOKEN'){ $xsrf=urldecode($p[6]); break; } } }
$headers = ['Content-Type: application/x-www-form-urlencoded']; if($xsrf) $headers[] = 'X-XSRF-TOKEN: '.$xsrf;
req('POST',$base.'/register',$jar,$post,$headers,120);
