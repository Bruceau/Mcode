<?php
/**
 * M-Code(明哥验证码) 1.0 for Typecho
 *
 * @package Mcode
 * @author Mingo
 * @version 1.0.0
 * @link https://0x50j.com/
 */

class Mcode_Plugin implements Typecho_Plugin_Interface
{
	/* 激活插件方法 */
     public static function activate() {
         Typecho_Plugin::factory('mc')->render = array('Mcode_Plugin', 'render');
         Typecho_Plugin::factory('mc')->verify = array('Mcode_Plugin', 'verify');
     }
     
     /* 禁用插件方法 */
     public static function deactivate() {

     }
      
     /* 插件配置方法 */
     public static function config(Typecho_Widget_Helper_Form $form) {
          // $mcode_string = new Typecho_Widget_Helper_Form_Element_Text('mcode_string', NULL, '0123456789', _t('验证码字符集'));
          // $types = array(
          //    'number' => '仅数字',
          //    'sen' => '仅英文(小写)',
          //    'ben' => '仅英文(大写)',
          //    'all' => '数字+英文(大小写)',
          // );
          // $mcode_type = new Typecho_Widget_Helper_Form_Element_Select('mcode_type', $types, 'number', _t('验证类型'));
          // $form->addInput($mcode_string);
          // $form->addInput($mcode_type);
     }
      
     /* 个人用户的配置方法 */
     public static function personalConfig(Typecho_Widget_Helper_Form $form) {

     }
      
     /* 插件实现方法 */
     public static function render() {
          echo '<p style="text-align: left;">';
          echo '<label for="captcha" class="sr-only">验证码</label>';
          echo '<input type="text" id="captcha" name="captcha" placeholder="验证码" class="text-l w-100" style="width: 65%;" onclick="getKey();" autofocus />';
          echo '<img width="100" border="0" id="vPic" align="absmiddle" class="yzmimg" height="38" onclick="getKey();" alt="(点选此处产生新验证码)" src="../usr/plugins/Mcode/lib/imgyzm.png" style="position: absolute;z-index: 2;border: 1px solid #d9d9d600;">';
          echo '</p>';
          echo '<script>function getKey() {document.getElementById("vPic").src="../usr/plugins/Mcode/lib/class.mcodelib.php?"+new Date().getTime();}</script>';
     }

    /* 验证方法 */
    public static function verify($loginobj) {
        session_start();
        $requestres = array(
            'statusMsg' => '',
            'empty' => '请进行验证',
            'failed' => '验证失败',
            'success' => '验证通过',
            'down' => '请求超时，请重试',
            'error' => '服务器异常，请重试'
        );
        $data = $loginobj->request->from('captcha');
        if (empty($data['captcha'])) {
            $requestres['statusMsg'] = 'empty';
            return $requestres;
        } elseif (empty($_SESSION['captcha'])) {
            $requestres['statusMsg'] = 'error';
            return $requestres;
        } elseif ($data['captcha'] == $_SESSION['captcha']) {
            $requestres['statusMsg'] = 'success';
            return $requestres;
        } else {
            $requestres['statusMsg'] = 'failed';
            return $requestres;
        }
    }
}