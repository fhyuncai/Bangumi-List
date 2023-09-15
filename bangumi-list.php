<?php

/**
 * Plugin Name: Bangumi 追番列表
 * Plugin URI: https://github.com/fhyuncai/Bangumi-List
 * Description: 追番列表插件，使用短代码 [bangumi] 即可展示追番列表
 * Version: 1.0.1
 * Author: FHYunCai
 * Author URI: https://yuncaioo.com
 */

defined('ABSPATH') or exit;
define('BGMLIST_VER', '1.0.1');

require_once('bangumi-api.php');

class bangumiList
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'bangumiInit']);
        add_shortcode('bangumi', [$this, 'bangumiShow']);
        add_action('wp_enqueue_scripts', [$this, 'bangumiScripts']);
    }

    public function bangumiInit()
    {
        $options = $this->_getOption();

        add_options_page('Bangumi 追番列表', 'Bangumi 追番列表', 'manage_options', 'bangumi_list_setting', [$this, 'bangumiOption']);

        if (isset($_POST['bangumi_submit'])) {
            $options['bangumiID'] = stripslashes($_POST['bangumiID']);
            if ($_POST['globalScripts']) {
                $options['globalScripts'] = true;
            } else {
                $options['globalScripts'] = false;
            }
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

    public function bangumiOption()
    {
        require_once('option.php');
    }

    public function bangumiShow($atts, $content = "")
    {
        // TODO 修改为文件读取形式
        return '<div id="bangumi_list_content">
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
        getBangumiData();
    </script>
    ';
    }

    public function bangumiScripts()
    {
        $options = $this->_getOption();

        if ((bool)$options['globalScripts'] === false) {
            global $post;//, $posts;
            //foreach ($posts as $post) {
                if (has_shortcode($post->post_content, 'bangumi')) {
                    $this->_loadScripts();
                    //break;
                }
            //}
        } else {
            $this->_loadScripts();
        }
    }

    private function _getOption()
    {
        $options = get_option('bangumi_list');
        if (!is_array($options)) {
            $options['bangumiID'] = '';
            $options['color'] = '#ff8c83';
            $options['globalScripts'] = true;
            $options['isCache'] = false;
            $options['isProxy'] = false;
            $options['singleItemNum'] = 10;
            $options['singleNavNum'] = 3;
            update_option('bangumi_list', $options);
        }
        return $options;
    }

    private function _loadScripts()
    {
        wp_enqueue_style('bangumi-list', plugins_url('css/bangumi_list.css', __FILE__), false, BGMLIST_VER);
        wp_enqueue_script('bangumi-list', plugins_url('js/bangumi_list.js', __FILE__), false, BGMLIST_VER);
        wp_localize_script('bangumi-list', 'BangumiList', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'version' => BGMLIST_VER,
        ]);
    }
}


new bangumiList();
