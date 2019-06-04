# Mcode
**需要修改文件1：**

    /admin/login.php

在代码这些中

	<form action="<?php $options->loginAction(); ?>" method="post" name="login" role="form">
        <p>
        	<label for="name" class="sr-only"><?php _e('用户名'); ?></label>
        	<input type="text" id="name" name="name" value="<?php echo $rememberName; ?>" placeholder="<?php _e('用户名'); ?>" class="text-l w-100" autofocus />
        </p>
        <p>
        	<label for="password" class="sr-only"><?php _e('密码'); ?></label>
        	<input type="password" id="password" name="password" class="text-l w-100" placeholder="<?php _e('密码'); ?>" />
        </p>
        <p class="submit">
            <button type="submit" class="btn btn-l w-100 primary"><?php _e('登录'); ?></button>
        	<input type="hidden" name="referer" value="<?php echo htmlspecialchars($request->get('referer')); ?>" />
        </p>
        <p>
        	<label for="remember"><input type="checkbox" name="remember" class="checkbox" value="1" id="remember" /> <?php _e('下次自动登录'); ?></label>
        </p>
    </form>

增加 '<?php Typecho_Plugin::factory('mc')->render(); ?>'

	<form action="<?php $options->loginAction(); ?>" method="post" name="login" role="form">
        <p>
        	<label for="name" class="sr-only"><?php _e('用户名'); ?></label>
        	<input type="text" id="name" name="name" value="<?php echo $rememberName; ?>" placeholder="<?php _e('用户名'); ?>" class="text-l w-100" autofocus />
        </p>
        <p>
        	<label for="password" class="sr-only"><?php _e('密码'); ?></label>
        	<input type="password" id="password" name="password" class="text-l w-100" placeholder="<?php _e('密码'); ?>" />
        </p>

        //添加到这里
        <?php Typecho_Plugin::factory('mc')->render(); ?>

        <p class="submit">
            <button type="submit" class="btn btn-l w-100 primary"><?php _e('登录'); ?></button>
        	<input type="hidden" name="referer" value="<?php echo htmlspecialchars($request->get('referer')); ?>" />
        </p>
        <p>
        	<label for="remember"><input type="checkbox" name="remember" class="checkbox" value="1" id="remember" /> <?php _e('下次自动登录'); ?></label>
        </p>
    </form>


**需要修改文件2：**
/var/Widget/Login.php

在

    /** 如果已经登录 */
    if ($this->user->hasLogin()) {
        /** 直接返回 */
        $this->response->redirect($this->options->index);
    }

下面添加

    $response = Typecho_Plugin::factory('mc')->verify($this);
    //判断验证码是否开启 且有没有通过验证
    if (!empty(Helper::options()->plugins['activated']['Mcode']) && $response['statusMsg'] != 'success') {
        $this->widget('Widget_Notice')->set($response[$response['statusMsg']]);
        $this->response->goBack();
    }