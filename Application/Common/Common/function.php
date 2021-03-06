<?php

/**
 * 公共方法
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */

/**
 * [FileUploadError 文件上传错误校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-04-12T17:21:51+0800
 * @param    [string]     $name [表单name]
 * @return   [mixed]            [true | 错误信息]
 */
function FileUploadError($name)
{
    // 是否存在该name表单
    if(empty($_FILES[$name]))
    {
        return L('common_select_file_tips');
    }

    // 是否正常
    if($_FILES[$name]['error'] == 0)
    {
        return true;
    }

    // 错误码对应的错误信息
    $file_error_list = L('common_file_upload_error_list');
    if(isset($file_error_list[$_FILES[$name]['error']]))
    {
        return $file_error_list[$_FILES[$name]['error']];
    }
    return L('common_unknown_error').'[file error '.$_FILES[$name]['error'].']';
}

/**
 * [ScienceNumToString 科学数字转换成原始的数字]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-04-06T17:21:51+0800
 * @param    [int]   $num [科学数字]
 * @return   [string]     [数据原始的值]
 */
function ScienceNumToString($num)
{
    if(stripos($num, 'e') === false) return $num;

    // 出现科学计数法，还原成字符串 
    $num = trim(preg_replace('/[=\'"]/','',$num,1),'"');
    $result = ''; 
    while($num > 0)
    { 
        $v = $num-floor($num/10)*10; 
        $num = floor($num/10); 
        $result   =   $v.$result; 
    }
    return $result; 
}


/**
 * [MyConfigInit 系统配置信息初始化]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-01-03T21:36:55+0800
 * @param    [int] $state [是否更新数据,0否,1是]
 */
function MyConfigInit($state = 0)
{
    $key = C('cache_common_my_config_key');
    $data = S($key);
    if($state == 1 || empty($data))
    {
        // 所有配置
        $data = M('Config')->getField('only_tag,value');

        // 数据存储
        S($key, $data);
    }
}

/**
 * [GetClientIP 客户端ip地址]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-02-09T12:53:13+0800
 * @param    [boolean]        $long [是否将ip转成整数]
 * @return   [string|int]           [ip地址|ip地址整数]
 */
function GetClientIP($long = false)
{
    $onlineip = ''; 
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
    { 
        $onlineip = getenv('HTTP_CLIENT_IP'); 
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
    {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR'); 
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
    {
        $onlineip = getenv('REMOTE_ADDR'); 
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
    {
        $onlineip = $_SERVER['REMOTE_ADDR']; 
    } 
    if($long)
    {
        $onlineip = sprintf("%u", ip2long($onlineip));
    }
    return $onlineip;
}

/**
 * [DelDirFile 删除指定目录下的所有文件]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-01-11T18:30:37+0800
 * @param    [string]     $dir_name   [目录地址]
 * @param    [boolean]    $is_del_dir [是否删除目录（默认false）]
 * @return   [boolean]                [成功true, 失败false]
 */
function DelDirFile($dir_name, $is_del_dir = false)  
{
    $error = 0;
    if($handle = opendir($dir_name))
    {
        while(false !== ($item = readdir($handle)))
        {
            if($item != '.' && $item != '..' )
            {
                if(is_dir("{$dir_name}/{$item}"))
                {
                    DelDirFile("$dir_name/$item", $is_del_dir);  
                } else {
                    if(!is_writable("$dir_name/$item") || !unlink("$dir_name/$item"))
                    {
                        $error++;
                    }
                }
            }
        }
        
        // 关闭目录句柄
        closedir($handle);

        // 是否删除目录
        if($is_del_dir == true && !rmdir($dir_name))
        {
            $error++;
        }
    }
    return ($error == 0);
}

/**
 * [UrlParamJoin url参数拼接]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-01-09T23:33:44+0800
 * @param    [array]      $param [url参数一维数组]
 * @return   [string]            [url参数字符串]
 */
function UrlParamJoin($param)
{
    $string = '';
    if(!empty($param) && is_array($param))
    {
        foreach($param as $k=>$v)
        {
            if(is_string($v))
            {
                $string .= $k.'='.$v.'&';
            }
        }
        if(!empty($string))
        {
            $url_model= C('URL_MODEL');
            $join_tag = ($url_model == 0 || $url_model == 3) ? '&' : '?';
            $string = $join_tag.substr($string, 0, -1);
        }
    }
    return $string;
}

/**
 * [MyC 读取站点配置信息]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-29T17:17:25+0800
 * @param    [string]    $key           [索引名称]
 * @param    [mixed]     $default       [默认值]
 * @param    [boolean]   $mandatory     [是否强制校验值,默认false]
 * @return   [mixed]                    [配置信息值,没找到返回null]
 */
function MyC($key, $default = null, $mandatory = false)
{
    $data = S(C('cache_common_my_config_key'));
    if($mandatory === true)
    {
        return empty($data[$key]) ? $default : $data[$key];
    }
    return isset($data[$key]) ? $data[$key] : $default;
}

/**
 * [EmptyDir 清空目录下所有文件]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-21T19:25:57+0800
 * @param    [string]    $dir_path [目录地址]
 * @return   [boolean]             [成功true, 失败false]
 */
function EmptyDir($dir_path)
{
    if(is_dir($dir_path))
    {
        $dn = @opendir($dir_path);
        if($dn !== false)
        {
            while(false !== ($file = readdir($dn)))
            {
                if($file != '.' && $file != '..')
                {
                    if(!unlink($dir_path.$file))
                    {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }
    return true;
}

/**
 * [Utf8Strlen 计算符串长度（中英文一致）]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-13T21:34:09+0800
 * @param    [string]      $string [需要计算的字符串]
 * @return   [int]                 [字符长度]
 */
function Utf8Strlen($string = null)
{
    preg_match_all("/./us", $string, $match);
    return count($match[0]);
}

/**
 * [IsMobile 是否是手机访问]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-05T10:52:20+0800
 * @return  [boolean] [手机访问true, 则false]
 */
function IsMobile()
{
    /* 如果有HTTP_X_WAP_PROFILE则一定是移动设备 */
    if(isset($_SERVER['HTTP_X_WAP_PROFILE'])) return true;
    
    /* 此条摘自TPM智能切换模板引擎，适合TPM开发 */
    if(isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT']) return true;
    
    /* 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息 */
    if(isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], 'wap') !== false) return true;
    
    /* 判断手机发送的客户端标志,兼容性有待提高 */
    if(isset($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array(
            'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipad','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
        );
        /* 从HTTP_USER_AGENT中查找手机浏览器的关键字 */
        if(preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }

    /* 协议法，因为有可能不准确，放到最后判断 */
    if(isset($_SERVER['HTTP_ACCEPT']))
    {
        /* 如果只支持wml并且不支持html那一定是移动设备 */
        /* 如果支持wml和html但是wml在html之前则是移动设备 */
        if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) return true;
    }
    return false;
}


/**
 * [Is_Json 校验json数据是否合法]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $jsonstr [需要校验的json字符串]
 * @return   [boolean] [合法true, 则false]
 */
function Is_Json($jsonstr)
{
    if(PHP_VERSION > 5.3)
    {
        json_decode($jsonstr);
        return (json_last_error() == JSON_ERROR_NONE);
    } else {
        return is_null(json_decode($jsonstr)) ? false : true;
    }
}

/**
 * [Curl_Post curl模拟post]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $url  [请求地址]
 * @param    [array]  $post [发送的post数据]
 */
function Curl_Post($url, $post)
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post,
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * [Fsockopen_Post fsockopen方式]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $url  [url地址]
 * @param    [string] $data [发送参数]
 */
function Fsockopen_Post($url, $data = '')
{
    $row = parse_url($url);
    $host = $row['host'];
    $port = isset($row['port']) ? $row['port'] : 80;
    $file = $row['path'];
    $post = '';
    while (list($k,$v) = each($data)) 
    {
        if(isset($k) && isset($v)) $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
    }
    $post = substr( $post , 0 , -1 );
    $len = strlen($post);
    $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
    if (!$fp) {
        return "$errstr ($errno)\n";
    } else {
        $receive = '';
        $out = "POST $file HTTP/1.0\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Content-Length: $len\r\n\r\n";
        $out .= $post;    
        fwrite($fp, $out);
        while (!feof($fp)) {
          $receive .= fgets($fp, 128);
        }
        fclose($fp);
        $receive = explode("\r\n\r\n",$receive);
        unset($receive[0]);
        return implode("",$receive);
    }
}

/**
 * [Xml_Array xml转数组]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [xml] $xmlstring [xml数据]
 * @return   [array]          [array数组]
 */
function Xml_Array($xmlstring) {
    return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
}


/**
 * [CheckMobile 手机号码格式校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [int] $mobile [手机号码]
 * @return   [boolean]     [正确true，失败false]
 */
function CheckMobile($mobile)
{
    return (preg_match('/'.L('common_regex_mobile').'/', $mobile) == 1) ? true : false;
}

/**
 * [CheckEmail 电子邮箱格式校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $email  [电子邮箱]
 * @return   [boolean]        [正确true，失败false]
 */
function CheckEmail($email)
{
    return (preg_match('/'.L('common_regex_email').'/', $email) == 1) ? true : false;
}

/**
 * [CheckIdCard 身份证号码格式校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $number [身份证号码]
 * @return   [boolean]        [正确true，失败false]
 */
function CheckIdCard($number)
{
    return (preg_match('/'.L('common_regex_id_card').'/', $number) == 1) ? true : false;
}

/**
 * [CheckPrice 价格格式校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [float]  $price  [价格]
 * @return   [boolean]        [正确true，失败false]
 */
function CheckPrice($price)
{
    return (preg_match('/'.L('common_regex_price').'/', $price) == 1) ? true : false;
}


/**
 * [CheckIp ip校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $ip [ip]
 */
function CheckIp($ip)
{
    return (preg_match('/'.L('common_regex_ip').'/', $ip) == 1) ? true : false;
}

/**
 * [CheckUrl url校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $url [url地址]
 */
function CheckUrl($url)
{
    return (preg_match('/'.L('common_regex_url').'/', $url) == 1) ? true : false;
}

/**
 * [CheckUserName 用户名校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param 	 [string] $string [用户名]
 * @return 	 [boolean]        [成功true, 失败false]
 */
function CheckUserName($string)
{
    return (preg_match('/'.L('common_regex_username').'/', $string) == 1) ? true : false;
}

/**
 * [CheckSort 排序值校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [int] $number [排序值]
 * @return   [boolean]     [成功true, 失败false]
 */
function CheckSort($number)
{
    $temp = intval($number);
    return ($temp >= 0 && $temp <= 255);
}

/**
 * [CheckLoginPwd 密码格式校验]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param    [string] $string [登录密码]
 * @return   [boolean]        [正确true, 错误false]
 */
function CheckLoginPwd($string)
{
    return (preg_match('/'.L('common_regex_pwd').'/', $string) == 1) ? true : false;
    // $len = strlen($string);
    // return ($len >= 6 && $len <= 18);
}

/**
 * [Sms_Code_Send 验证码通道]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param  	 [staing] $content      [内容]
 * @param  	 [string] $mobile_phone [手机号码]
 * @return 	 [boolean]              [成功true, 失败false]
 */
function Sms_Code_Send($content, $mobile_phone)
{
    $post = array(
      'apikey'  =>  '1111',
      'text'    =>  $content,
      'mobile'  =>  $mobile_phone,
    );
    $result = json_decode(Fsockopen_Post('http://yunpian.com/v1/sms/send.json', $post), true);
    if(empty($result)) return false;
    return ($result['msg'] == 'OK');
}

/**
 * [IsExistWebImg 检测一张网络图片是否存在]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param  	 [string] $url [图片地址]
 * @return 	 [boolean]     [存在true, 则false]
 */
function IsExistWebImg($url)
{
    if(!empty($url))
    {
        $content = get_headers($url, 1);
        if(!empty($content[0]))
        {
            return preg_match('/200/',$content[0]);
        }
    }
    return false;
}

/**
 * [GetNumberCode 随机数生成生成]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param  	 [int] $length [生成位数]
 * @return 	 [int]         [生成的随机数]
 */
function GetNumberCode($length = 6)
{
    $code = '';
    for($i=0; $i<intval($length); $i++) $code .= rand(0, 9);
    return $code;
}

/**
 * [LoginPwdEncryption 登录密码加密]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param  	 [string] $pwd  [需要加密的密码]
 * @param  	 [string] $salt [配合密码加密的随机数]
 * @return 	 [string]       [加密后的密码]
 */
function LoginPwdEncryption($pwd, $salt)
{
	return md5($salt.trim($pwd));
}

/**
 * [PwdPayEncryption 支付密码加密]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param  	 [string] $pwd  [需要加密的密码]
 * @param  	 [string] $salt [配合密码加密的随机数]
 * @return 	 [string]       [加密后的密码]
 */
function PwdPayEncryption($pwd, $salt)
{
    return md5(md5(trim($pwd).$salt));
}

/**
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * [PwdStrength 密码强度校验]
 * @param  	 [string] $pwd [需要校验的密码]
 * @return 	 [int]         [密码强度值0~10]
 */
function PwdStrength($pwd)
{ 
    $score = 0; 
    if(preg_match("/[0-9]+/", $pwd)) $score ++; 
    if(preg_match("/[0-9]{3,}/", $pwd)) $score ++;
    if(preg_match("/[a-z]+/", $pwd)) $score ++;
    if(preg_match("/[a-z]{3,}/", $pwd)) $score ++;
    if(preg_match("/[A-Z]+/", $pwd)) $score ++;
    if(preg_match("/[A-Z]{3,}/", $pwd)) $score ++;
    if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $pwd)) $score += 2;
    if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/", $pwd)) $score ++ ;
    if(strlen($pwd) >= 10) $score ++;
    return $score;
}
 
/**
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 *  $lng = '116.655540';
 *  $lat = '39.910980';
 *  $squares = returnSquarePoint($lng, $lat);
 *       
 *  print_r($squares);
 *  $info_sql = "select id,locateinfo,lat,lng from `lbs_info` where lat<>0 and lat>{$squares['right-bottom']['lat']} and lat<{$squares['left-top']['lat']} and lng>{$squares['left-top']['lng']} and lng<{$squares['right-bottom']['lng']} ";
 *  计算某个经纬度的周围某段距离的正方形的四个点
 *
 *  param lng float 经度
 *  param lat float 纬度
 *  param Distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
 *  return array 正方形的四个点的经纬度坐标
 */
function ReturnSquarePoint($lng, $lat, $Distance = 1.2)
{
    /* 地球半径，平均半径为6371km */
    $Radius = 6371;

    $d_lng =  2 * asin(sin($Distance / (2 * $Radius)) / cos(deg2rad($lat)));
    $d_lng = rad2deg($d_lng);

    $d_lat = $Distance/$Radius;
    $d_lat = rad2deg($d_lat);

    return array(
        'left-top'=>array('lat'=>$lat + $d_lat,'lng'=>$lng-$d_lng),
        'right-top'=>array('lat'=>$lat + $d_lat, 'lng'=>$lng + $d_lng),
        'left-bottom'=>array('lat'=>$lat - $d_lat, 'lng'=>$lng - $d_lng),
        'right-bottom'=>array('lat'=>$lat - $d_lat, 'lng'=>$lng + $d_lng)
    );
}

/**
 * [Authcode 明文或密文]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-03T21:58:54+0800
 * @param  	 [string]  $string    [明文或密文]
 * @param  	 [string]  $operation [加密ENCODE, 解密DECODE]
 * @param  	 [string]  $key       [密钥]
 * @param  	 [integer] $expiry    [密钥有效期]
 * @return 	 [string]             [加密或解密后的数据]
 */
function Authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $ckey_length = 4;
  
    // 密匙
    // $GLOBALS['discuz_auth_key'] 这里可以根据自己的需要修改
    $key = md5($key ? $key : 'devil'); 
  
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

/**
 * [SS 设置缓存]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  1.0.0
 * @datetime 2017-09-24T19:01:00+0800
 * @param    [string]              $key  [缓存key]
 * @param    [mixed]               $data [需要存储的数据]
 * @return   [boolean]                   [成功true, 失败false]
 */
function SS($key, $data)
{
    if(empty($key) || empty($data))
    {
        return false;
    }
    $dir = C('data_cache_dir');
    if(!is_dir($dir))
    {
        mkdir($dir, 0777, true);
    }
    return (file_put_contents($dir.md5($key).'.txt', serialize($data)) !== false);
}

/**
 * [GS 获取缓存]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  1.0.0
 * @datetime 2017-09-24T18:54:54+0800
 * @param    [string]          $key             [缓存key]
 * @param    [integer]         $expires_time    [默认过期时间0长期有效（单位秒）]
 * @param    [boolean]         $is_filem_time   [是否返回文件上一次更新时间]
 * @return   [boolean|mixed]                    [没数据false, 则数据]
 */
function GS($key, $expires_time = 0, $is_filem_time = false)
{
    if(empty($key))
    {
        return false;
    }
    $file_name = C('data_cache_dir').md5($key).'.txt';
    if(file_exists($file_name))
    {
        /* 文件上次修改时间 */
        $filem_time = filemtime($file_name);

        /* 如果过期时间大于0则判断数据是否过期 */
        $expires_time = intval($expires_time);
        if($expires_time > 0)
        {
            if($filem_time+$expires_time < time())
            {
                return false;
            }
        }
        
        $data = unserialize(file_get_contents($file_name));
        if(empty($data))
        {
            return false;
        } else {
            if($is_filem_time == true)
            {
                $data['filemtime'] = $filem_time;
            }
            return $data;
        }
    }
    return false;
}

/**
 * [DS 删除缓存]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  1.0.0
 * @datetime 2017-09-24T19:01:00+0800
 * @param    [string]              $key  [缓存key]
 * @return   [boolean]                   [成功true, 失败false]
 */
function DS($key)
{
    if(!empty($key))
    {
        $file_name = C('data_cache_dir').md5($key).'.txt';
        if(file_exists($file_name))
        {
            return unlink($file_name);
        }
    }
    return false;
}

/**
 * [params_checked 参数校验方法]
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  1.0.0
 * @datetime 2017-12-12T15:26:13+0800
 * @param    [array]                   $data   [原始数据]
 * @param    [array]                   $params [校验数据]
 * @return   [boolean|string]                  [成功true, 失败 错误信息]
 */
function params_checked($data, $params)
{
    if (empty($data) || empty($params) || !is_array($data) || !is_array($params)) {
        return '内部调用参数配置有误';
    }

    foreach ($params as $v) {
        if (empty($v['key_name']) || empty($v['error_msg'])) {
            return '内部调用参数配置有误';
        }

        $checked_type = isset($v['checked_type']) ? $v['checked_type'] : 'isset';
        switch ($checked_type) {
            case 'isset' :
                if (!isset($data[$v['key_name']])) {
                    return $v['error_msg'];
                }
                break;

            case 'empty' :
                if (empty($data[$v['key_name']])) {
                    return $v['error_msg'];
                }
                break;

            case 'in' :
                if (empty($v['checked_data']) || !is_array($v['checked_data'])) {
                    return '内部调用参数配置有误';
                }
                if (!isset($data[$v['key_name']]) || !in_array($data[$v['key_name']], $v['checked_data'])) {
                    return $v['error_msg'];
                }
        }
    }
    return true;
}
?>