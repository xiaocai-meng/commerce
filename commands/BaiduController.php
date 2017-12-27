<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaiduController extends Controller
{

    public function actionIndex()
    {
        $dir = dirname(dirname(__FILE__)).'/vendor/1';
        $dir1 = '/Users/xiaocai/Downloads/heliaoxinxia';
        //$dirall = glob($dir.'/*');
        $res = $this->read_all_dir($dir1);
        $this->arr($res);
        //print_r($res);
//        foreach ($res as $ke => &$v)
//        {
//            $v = base64_encode(basename($v));
//        }
//        print_r($res);
    }

    function read_all_dir ( $dir )
    {
        $result = array();
        $handle = opendir($dir);
        if ( $handle )
        {
            while ( ( $file = readdir ( $handle ) ) !== false )
            {
                if ( $file != '.' && $file != '..')
                {
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if ( is_dir ( $cur_path ) )
                    {
                        $result['dir'][$cur_path] = $this->read_all_dir ( $cur_path );
                    }
                    else
                    {
                        $result['file'][] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }

    function my_dir($dir) {
        $files = array();
        if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
            while(($file = readdir($handle)) !== false) {
                if($file != ".." && $file != ".") { //排除根目录；
                    if(is_dir($dir."/".$file)) { //如果是子文件夹，就进行递归
                        $files[$file] = $this->my_dir($dir."/".$file);
                    } else { //不然就将文件的名字存入数组；
                        $files[] = $file;
                    }

                }
            }
            closedir($handle);
            return $files;
        }
    }

    function arr($arr)
    {
        foreach ($arr as $type => $v)
        {
            if ($type == 'file')
            {
                foreach ($v as $b => $c)
                {
                    $this->execl_to_csv($c);
                }
            } else {
                foreach ($v as $d => $e)
                {
                    $this->arr($e);
                }
            }

        }
    }

    function execl_to_csv($filename)
    {
        $postfix = strstr($filename, '.');
        if ($postfix == 'xlsx')
        {
            $type = 'Excel2007';
        }
        if ($postfix == 'xls')
        {
            $type = 'Excel5';
        }
        //$filename = '/Users/xiaocai/Downloads/PHPExcel-1.8/2.xlsx';
        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($filename);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save(str_replace($postfix, '.csv',$filename));
    }

}

