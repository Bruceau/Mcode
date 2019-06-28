<?php
/**
 * M-Code(明哥验证码) 2.1 for Typecho </br> Update time: <code style="padding: 2px 4px; font-size: 90%; color: #c7254e; background-color: #f9f2f4; border-radius: 4px;">2019-06-29</code>
 *
 * @package Mcode
 * @author Mingo
 * @version 2.1.0
 * @link https://0x50j.com/
 */

class Mcode_Plugin implements Typecho_Plugin_Interface
{
	/* 激活插件方法 */
     public static function activate() {
         Typecho_Plugin::factory('mc')->render = array('Mcode_Plugin', 'render');
         Typecho_Plugin::factory('mc')->verify = array('Mcode_Plugin', 'verify');
         return _t('插件启用成功,请在login.php代码登陆的位置插入嵌入点代码!!!');
     }
     
     /* 禁用插件方法 */
     public static function deactivate() {
        return _t('插件禁用成功');
     }
      
     /* 插件配置方法 */
     public static function config(Typecho_Widget_Helper_Form $form) {
          $mcode_int = new Typecho_Widget_Helper_Form_Element_Text('mcode_int', NULL, '4', _t('验证码字符数'), _t('验证码字符数，范围0~6，默认4'));
          $mcode_string = new Typecho_Widget_Helper_Form_Element_Text('mcode_string', NULL, '0123456789', _t('验证码字符集'), _t('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'));
          $access = array(
            'enable' => '启用',
            'disable' => '禁用',
          );
          $mcode_access = new Typecho_Widget_Helper_Form_Element_Select('mcode_access', $access, 'disable', _t('是否启用白名单'));
          $mcode_whitelist = new Typecho_Widget_Helper_Form_Element_Text('mcode_whitelist', NULL, NULL, _t('IP白名单'), _t('例如:192.168.0.1,192.168.1.1  以英文逗号分隔'));
          $mcode_jump_url = new Typecho_Widget_Helper_Form_Element_Text('mcode_jump_url', NULL, 'https://resource.0x50j.com/', _t('跳转域名'), _t(' IP白名单未通过验证需要跳转的Url地址,例如:https://resource.0x50j.com/'));
          $form->addInput($mcode_int);
          $form->addInput($mcode_string);
          $form->addInput($mcode_access);
          $form->addInput($mcode_whitelist);
          $form->addInput($mcode_jump_url);
     }
      
     /* 个人用户的配置方法 */
     public static function personalConfig(Typecho_Widget_Helper_Form $form) {

     }
      
     /* 插件实现方法 */
     public static function render() {
          session_start();
          if (Typecho_Widget::widget('Widget_Options')->plugin('Mcode')->mcode_access == 'enable') {
            $client_ip=$_SERVER['REMOTE_ADDR'];
            $white_ip = explode(',',Typecho_Widget::widget('Widget_Options')->plugin('Mcode')->mcode_whitelist);
            $acc_result = 0;
            for ($index=0; $index<count($white_ip); $index++) {
                if ($client_ip == $white_ip[$index]) {
                  $acc_result = 1;
                  break;
                }
            }

            if ($acc_result == 0) {
              $jump_url = Typecho_Widget::widget('Widget_Options')->plugin('Mcode')->mcode_jump_url;
              header("location:$jump_url");
              die;
            }
          }
          $_SESSION['captcha_string'] = Typecho_Widget::widget('Widget_Options')->plugin('Mcode')->mcode_string;
          $_SESSION['captcha_int'] = Typecho_Widget::widget('Widget_Options')->plugin('Mcode')->mcode_int;
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