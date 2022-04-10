<?php


$file = $_FILES['file'];
if (is_uploaded_file($file['tmp_name'])){
	$arr = pathinfo($file['name']);
	$ext_suffix = $arr['extension'];
	$allow_suffix = array('jpg','gif','jpeg','png');
	if(!in_array($ext_suffix, $allow_suffix)){
		msg(['code'=> 1,'msg'=> '上传格式不支持']);
	}
	$new_filename = time().rand(100,1000).'.'.$ext_suffix;
	if (move_uploaded_file($file['tmp_name'], $new_filename)){
		//$data = upload('https://kfupload.alibaba.com/mupload'
		$data=upload($new_filename);
	}else{
		msg(['code'=> 1,'msg'=> '上传数据有误']);
	}

}else{
	msg(['code'=> 1,'msg'=> '上传数据有误']);
}

function upload($file_path)
{
    $url = 'https://kfupload.alibaba.com/kupload';
    $data = [];
    $data['scene'] = 'aeMessageCenterV2ImageRule';
    $data['name'] = $file_path;
    $data['file'] = new CURLFile(realpath($file_path));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $hothead[] = "Accept:application/json";
    $hothead[] = "Accept-Encoding:gzip,deflate,sdch";
    $hothead[] = "Accept-Language:zh-CN,zh;q=0.8";
    $hothead[] = "Connection:close";
    $ip = mt_rand(48, 140) . "." . mt_rand(10, 240) . "." . mt_rand(10, 240) . "." . mt_rand(10, 240);
    $hothead[] = 'CLIENT-IP:' . $ip;
    $hothead[] = 'X-FORWARDED-FOR:' . $ip;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $hothead);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Dalvik/2.1.0 (Linux; U; Android 10; ONEPLUS A5010 Build/QKQ1.191014.012)');
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $html = @curl_exec($ch);
    curl_close($ch);
    //exit($html);
    $json = @json_decode($html, true);
    @unlink($file_path);
    if ($json['code'] == '0') {
        msg(['code'=> 0,'msg'=> $json['url']]);
    }else{
        msg(['code'=> 1,'msg'=> '上传失败']);
    }
    return ;
}

function msg($data){
	exit(json_encode($data));
}