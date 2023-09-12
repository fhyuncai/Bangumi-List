<?php

/**
 * Plugin Name: Bangumi 追番列表
 * Plugin URI: https://github.com/fhyuncai/wordpress-bangumi-list
 * Description: 追番列表插件，使用短代码 [bangumi] 即可展示追番列表
 * Version: 1.0.0
 * Author: FHYunCai
 * Author URI: https://yuncaioo.com
 */

defined('ABSPATH') or exit;
define('BGMLIST_VER', '1.0.0');

require_once("bangumi-api.php");

class bangumiList
{
    function __construct()
    {
        add_action('admin_menu', [$this, 'initBangumi']);
        add_action('init', [$this, 'register_shortcodes']);
    }

    public function register_shortcodes()
    {
        add_shortcode('bangumi', [$this, 'outPut']);
    }

    function getOption()
    {
        $options = get_option('bangumi_list');
        if (!is_array($options)) {
            $options['bangumiID'] = '';
            $options['color'] = '#ff8c83';
            $options['isCache'] = false;
            $options['isProxy'] = false;
            $options['singleItemNum'] = 10;
            $options['singleNavNum'] = 3;
            update_option('bangumi_list', $options);
        }
        return $options;
    }

    function initBangumi()
    {
        $options = $this->getOption();
        add_options_page('Bangumi 追番列表', 'Bangumi 追番列表', 'manage_options', 'bangumi_list_setting', [$this, 'optionPage']);
        if (isset($_POST['bangumi_submit'])) {
            $options['bangumiID'] = stripslashes($_POST['bangumiID']);
            if ($_POST['isCache']) {
                $options['isCache'] = true;
            } else {
                $options['isCache'] = false;
            }
            if ($_POST['isProxy']) {
                $options['isProxy'] = true;
            } else {
                $options['isProxy'] = false;
            }
            if ($_POST['singleItemNum']) {
                $tempItemNum = stripslashes($_POST['singleItemNum']);
                if (is_numeric($tempItemNum)) {
                    $options['singleItemNum'] = (intval($tempItemNum) <= 0 ? 12 : intval($tempItemNum));
                } else {
                    $options['singleItemNum'] = 12;
                }
            }
            if ($_POST['singleNavNum']) {
                $tempNavNum = stripslashes($_POST['singleNavNum']);
                if (is_numeric($tempNavNum)) {
                    $options['singleNavNum'] = (intval($tempNavNum) <= 0 ? 3 : intval($tempNavNum));
                } else {
                    $options['singleNavNum'] = 3;
                }
            }
            $options['color'] = stripslashes($_POST['color']);
            update_option('bangumi_list', $options);
            echo '<div id="message" class="updated"><h4>设置已保存</h4></div>';
        } else if (isset($_POST['bangumi_clear'])) {
            $cachePath = plugin_dir_path(__FILE__) . 'BangumiCache.json';;
            if (is_file($cachePath)) @unlink($cachePath);
            echo '<div id="message" class="updated"><h4>缓存已清除</h4></div>';
        }
    }

    function optionPage()
    {
        require_once('option.php');
    }

    public function outPut($atts, $content = "")
    {
        // TODO 修改为文件读取形式
        return '<script src="' . plugins_url('js/bangumi_list.js', __FILE__) . '"></script>
            <link rel="stylesheet" type="text/css" href="' . plugins_url('css/bangumi_list.css', __FILE__) . ' " />
            <div id="bangumi_list_content">
        <div class="bangumi_loading">
            <div class="loading-anim">
                <div class="border out"></div>
                <div class="border in"></div>
                <div class="border mid"></div>
                <div class="circle">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
                <div class="bangumi_loading_text">追番数据加载中...</div>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
    <div id="bangumi_nav"><ui id="bangumi_list_nav"></ui></div>
    <script>
        let xmlhttp;
        function getBangumiData(){
            if(window.XMLHttpRequest)
            {
                xmlhttp=new XMLHttpRequest();
            }else{
                alert("where is my xmlreq?");
                return;
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    if(parseBangumiData)
                    {   
                        let bangumiData;
                        try{
                            bangumiData = JSON.parse(xmlhttp.responseText);
                            parseBangumiData(bangumiData);
                        }catch(e){
                            console.log(xmlhttp.responseText);
                            console.log(e);
                        }

                    }
                }
            }
            xmlhttp.open("get", "' . admin_url('admin-ajax.php') . '?action=GetBangumiData", true);
            xmlhttp.send();
        }
        getBangumiData();
    </script>
    ';
    }
}


new bangumiList();
