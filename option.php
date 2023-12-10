<?php

defined('ABSPATH') or exit;

$options = get_option('bangumi_list');

?>
<div class="wrap">
    <h2>Bangumi 追番列表 配置面板</h2>

    <!--<div id="message" class="updated"><p>Bangumi 追番列表 <?php echo BGMLIST_VER ?>版本更新日志：<br />
[无]无
</div>-->
    <form method="POST" action="">
        <input type="hidden" name="update_pluginoptions" value="true" />
        <div class="bangumi_panel" style="display:block;margin:0 20px">
            <table class="form-table">
                <tr>
                    <th>Bangumi ID</th>
                    <td><label>
                            <input type="text" name="bangumiID" style="width:272px;" value="<?php echo $options['bangumiID'] ?>" />
                        </label>
                        <p class="description">追番列表数据来源的 Bangumi UID 或 用户名，可在 Bangumi 的个人页面 URL 中找到</p>
                    </td>
                </tr>
                <tr>
                    <th>主题颜色</th>
                    <td><label>
                            <input type="color" name="color" value="<?php echo $options['color'] ?>">
                        </label>
                        <p class="description">进度条及标签颜色</p>
                    </td>
                </tr>
                <tr>
                    <th>单页番剧数量</th>
                    <td><label>
                            <input type="number" name="singleItemNum" value="<?php echo $options['singleItemNum'] ?>" />
                        </label></td>
                </tr>
                <tr>
                    <th>单页导航标签数量</th>
                    <td><label>
                            <input type="number" name="singleNavNum" value="<?php echo $options['singleNavNum'] ?>" />
                        </label>
                        <p class="description">若当前页为头尾，数量可能多于该数值</p>
                    </td>
                </tr>
                <tr style="display:none;">
                    <th>使用第三方接口</th>
                    <td><label>
                            <input type="checkbox" name="isProxy" <?php echo $options['isProxy'] ? 'checked' : '' ?> /><span> 启用</span>
                        </label>
                        <p class="description">实验项目，不建议启用</p>
                    </td>
                </tr>
                <tr>
                    <th>启用缓存</th>
                    <td><label>
                            <input type="checkbox" name="isCache" <?php echo $options['isCache'] ? 'checked' : '' ?> /><span> 启用</span>
                        </label></td>
                </tr>
                <tr>
                    <th>全局加载资源</th>
                    <td><label>
                            <input type="checkbox" name="globalScripts" <?php echo $options['globalScripts'] ? 'checked' : '' ?> /><span> 启用</span>
                        </label>
                        <p class="description">启用后无论是否使用了短代码都会加载CSS、JS资源，适合启用了 Pjax 的站点</p>
                    </td>
                </tr>
            </table>
        </div>
        <p class="submit">
            <input type="submit" name="bangumi_submit" class="button button-primary" onclick="submitBangumi()" value="保存设置" />
            <input type="submit" name="bangumi_clear" class="button button-secondary" value="清空缓存" />
        </p>
        <a href="https://github.com/fhyuncai/Bangumi-List" target="_blank">Bangumi-List</a> 版本: <?php echo BGMLIST_VER ?> 作者: <a href="https://yuncaioo.com" target="_blank">FHYunCai</a>
    </form>

    <style>
        .wrap th {
            font-weight: normal;
        }
    </style>
    <script>
        function submitBangumi() {
            jQuery(function($) {
                $("[name='bangumi_submit']").prop("disabled", true);
                $("[name='bangumi_submit']").val("正在保存…");
            });
        }
    </script>