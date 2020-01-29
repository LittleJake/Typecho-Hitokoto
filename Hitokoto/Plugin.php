<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 *
 * 一言挂件widget
 *
 * @package Typecho-Hitokoto
 * @author  LittleJake
 * @version 1.0.0
 * @link https://blog.littlejake.tk
 */
class Hitokoto_Plugin implements Typecho_Plugin_Interface
{
    private static $api = "https://v1.hitokoto.cn/?c=a";
    /**
     * 激活插件方法
     *
     * @return void
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array(
            'Hitokoto_Plugin',
            'getHitokoto',
        );

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
            array('1' => _t('是'),
                '0' => _t('否')),
            '1',
            _t('是否显示')
        );

        $form->addInput($display);
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
     * @return array
     * @throws
     */
    public static function getHitokoto()
    {
        $display = Typecho_Widget::widget('Widget_Options')
            ->plugin('Hitokoto')->display == 0?false:true;

        if(!$display)
            return null;

        $time = time();

        if(is_readable("./hitokoto.json")) {
            $json = @file_get_contents("./hitokoto.json");
            $json = json_decode($json, true);

            if($time - $json['time'] < 120)
                return "<strong>$json[hitokoto]</strong>\n
                        <p>————$json[from]</p>";
        }

        //curl获取json
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $json = curl_exec($ch);
        curl_close($ch);


        $json = json_decode($json, true);
        $json['time'] = $time;
        if(!@file_put_contents("./hitokoto.json", json_encode($json)))
            return "<p>请检查目录插件权限</p>";

        return "<strong>$json[hitokoto]</strong>\n
                        <p>————$json[from]</p>";
    }
}
