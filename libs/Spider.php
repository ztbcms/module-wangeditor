<?php
/**
 * Author: cycle_3
 */

namespace app\wangeditor\libs;

class Spider {

    /**
     * 批量更换本地图片
     * @param  string  $text 内容
     * @return mixed|string|string[]
     */
    public function copyText($text = ''){
        //匹配img标签的正则表达式
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']*?\/?\s*>/i';
        //匹配背景的url的正则表达式
        $preg2 = '/background-image:[ ]?url\(&quot;[\'"]?(.*?\.(?:png|jpg|jpeg|gif))/i';
        preg_match_all($preg, $text, $allImg);//这里匹配所有的img
        preg_match_all($preg2, $text, $allImg2);//这里匹配所有的背景img
        $imgList = array_merge($allImg[1],$allImg2[1]);
        if (empty($imgList)){
            return $text;
        }
        $imgList = array_unique($imgList);
        foreach ($imgList as $img_url){
           $save_path = $this->downloadImage($img_url,'d/wangeditor_file/'.date("Y-m-d"))['save_path'];
           if($save_path) {
               $new_img_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$save_path;
               $text = str_replace($img_url,$new_img_url,$text);
           }
        }
        return $text;
    }

    /**
     * 下载远程图片保存到本地
     * @param string $url  文件url
     * @param  string  $save_dir 保存文件目录
     * @param  string  $filename 保存文件名称
     * @param  int  $type 使用的下载方式
     * @return array
     */
    public function downloadImage($url = '',$save_dir = '',$filename = '', $type = 1){
        if(trim($url) == ''){
            return array(
                'file_name'=>'',
                'save_path'=>'',
                'error'=>1
            );
        }
        if(trim($save_dir) == ''){
            $save_dir='./';
        }

        if(trim($filename) == ''){//保存文件名
            $ext = strrchr($url,'.');
            if($ext != '.gif' && $ext!='.jpg' && $ext != '.webp'){
                return array( 'file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time().$this->GetRandStr(6).$ext;
        }

        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }

        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array(
                'file_name'=>'',
                'save_path'=>'',
                'error'=> 5 );
        }

        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }

        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);

        return array(
            'file_name'=>$filename,
            'save_path'=>$save_dir.$filename,
            'error'=>0);
    }

    /**
     * 字符串
     * @param $length
     * @return string
     */
    function GetRandStr($length){
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str)-1;
        $randstr = '';
        for ($i=0;$i<$length;$i++) {
            $num=mt_rand(0,$len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
}