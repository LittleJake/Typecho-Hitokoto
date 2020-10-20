<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 *
 * 一言挂件widget
 *
 * @package Typecho-Hitokoto
 * @author  LittleJake
 * @version 2.0.0
 * @link https://blog.littlejake.net
 */
class Hitokoto_Plugin implements Typecho_Plugin_Interface
{
    private static $api = "https://v1.hitokoto.cn/?";
    private static $api_inter = "https://international.v1.hitokoto.cn/?";
    private static $category = [];
    /**
     * 激活插件方法
     *
     * @return void
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('Hitokoto_Plugin');
        self::$category = [
            'a' => _t('动画'),
            'b' => _t('漫画'),
            'c' => _t('游戏'),
            'd' => _t('文学'),
            'e' => _t('原创'),
            'f' => _t('来自网络'),
            'g' => _t('其他'),
            'h' => _t('影视'),
            'i' => _t('诗词'),
            'j' => _t('网易云'),
            'k' => _t('哲学'),
            'l' => _t('抖机灵'),
        ];
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @access public
     * @return void
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
        $display = new Typecho_Widget_Helper_Form_Element_Radio(
            'display',
            [
                '1' => _t('是'),
                '0' => _t('否')
            ],
            '1',
            _t('是否显示一言')
        );

        $api_type = new Typecho_Widget_Helper_Form_Element_Radio(
            'api_type',
            [
                'intl' => _t('海外'),
                'cn' => _t('中国')
            ],
            'cn',
            _t('选择一言服务器')
        );
        $category = new Typecho_Widget_Helper_Form_Element_Checkbox(
            'category',
            self::$category,
            'a',
            _t('选择显示的一言类型')
        );
        $time = new Typecho_Widget_Helper_Form_Element_Text(
            'time',
            NULL,
            120,
            _t('本地一言缓存过期时间'),
            _t('默认为：120秒，尽量不要设置过快（参考QPS限制：国内3.5，国际：10）')
        );
        $template = new Typecho_Widget_Helper_Form_Element_Textarea(
            'template',
            NULL,
            <<<EOF
<div>
    <strong>{hitokoto}</strong>
    <p>{from}</p>
</div>
EOF
,
            _t('一言显示自定义模板'),
            _t('可用变量参考：<a href="https://developer.hitokoto.cn/sentence/#返回格式" target="_blank">https://developer.hitokoto.cn/sentence/#返回格式</a>')
        );
        $form->addInput($display);
        $form->addInput($category);
        $form->addInput($api_type);
        $form->addInput($time);
        $form->addInput($template);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     *
     * 获取一言
     *
     * @access public
     * @return string
     * @throws
     */
    public static function getHitokoto()
    {
        //TODO 添加类型参数、自定义class、tag参数
        $display = Typecho_Widget::widget('Widget_Options')
            ->plugin('Hitokoto')->display == 0?false:true;
        //处理类型参数
        $category = empty(Typecho_Widget::widget('Widget_Options')
            ->plugin('Hitokoto')->category)?'':"c=".implode("&c=", Typecho_Widget::widget('Widget_Options')
                ->plugin('Hitokoto')->category);
        $url = Typecho_Widget::widget('Widget_Options')
            ->plugin('Hitokoto')->api_type == 'cn'?self::$api:self::$api_inter;
        $expire = Typecho_Widget::widget('Widget_Options')
            ->plugin('Hitokoto')->time;

        if(!$display)
            return null;

        $time = time();

        if(is_readable("./hitokoto.json")) {
            $json = @file_get_contents("./hitokoto.json");
            $json = json_decode($json, true);

            if($time - $json['time'] < intval($expire))
                return self::format($json);
        }

        //curl获取json
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.$category);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $json = curl_exec($ch);
        curl_close($ch);


        $json = json_decode($json, true);
        $json['time'] = $time;
        if(!@file_put_contents("./hitokoto.json", json_encode($json)))
            return "<p>请检查目录插件权限</p>";

        return self::format($json);
    }

    /**
     * 用于处理、格式化json不同类型的数据
     *
     * @param $array
     * @return string|string[]
     * @throws Typecho_Exception
     */
    public static function format($array){
        $template = Typecho_Widget::widget('Widget_Options')
            ->plugin('Hitokoto')->template;

        foreach ($array as $k => $v)
            switch ($k){
                case 'created_at':
                case 'time':
                    $template = str_replace("{{$k}}", htmlspecialchars(date('Y-m-d H:i:s',$v)), $template);
                    break;
                case 'type':
                    $template = str_replace("{{$k}}", htmlspecialchars(self::$category[$v]), $template);
                    break;
                default:
                    $template = str_replace("{{$k}}", htmlspecialchars($v), $template);
            }

        return $template;
    }
}
