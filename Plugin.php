<?php

namespace TypechoPlugin\LoginCaptcha;

use Typecho\Common;
use Typecho\Cookie;
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Request as HttpRequest;
use Typecho\Response as HttpResponse;
use Typecho\Widget\Request;
use Typecho\Widget\Response;
use Utils\Helper;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Typecho 后台登录页面增加验证码保护
 *
 * @package LoginCaptcha
 * @author Ryan
 * @version 1.0.1
 * @link https://doufu.ru
 *
 */
class Plugin implements PluginInterface
{
    public static function activate()
    {
        \Typecho\Plugin::factory('index.php')->begin = [__CLASS__, 'hookRoute'];
        Helper::addRoute('login-captcha', '/login-captcha', Action::class, 'renderCaptcha');
        \Typecho\Plugin::factory('admin/footer.php')->end = [__CLASS__, 'addCaptcha'];
    }

    public static function deactivate()
    {
        Helper::removeRoute('login-captcha');
    }

    public static function config(Form $form)
    {
        // 插件配置项
    }

    public static function personalConfig(Form $form)
    {
        // 个人配置项
    }

    public static function hookRoute()
    {
        $request = new Request(HttpRequest::getInstance());
        $response = new Response(HttpRequest::getInstance(), HttpResponse::getInstance());
        $pathinfo = $request->getPathInfo();
        if (preg_match("#/action/login#", $pathinfo)) {
            if (!isset($_SESSION['captcha']) || strtolower($_POST['captcha']) != $_SESSION['captcha']) {
                Notice::alloc()->set(_t('验证码错误'), 'error');
                Cookie::set('__typecho_remember_captcha', '');
                $response->goBack();
            }
        }
    }

    public static function addCaptcha()
    {
        $request = new Request(HttpRequest::getInstance());
//        $response = new Response(HttpRequest::getInstance(), HttpResponse::getInstance());
        $pathinfo = $request->getRequestUri();
        $loginPath = Common::url('login.php', defined('__TYPECHO_ADMIN_DIR__') ?
            __TYPECHO_ADMIN_DIR__ : '/admin/');
        $secureUrl = Helper::security()->getIndex('login-captcha');
        if (stripos($pathinfo, $loginPath) === 0) {
            ?>
            <script>
                (function () {
                    let _src = '<?php echo $secureUrl ?>';
                    const src = _src + (_src.includes('?') ? '&t=' : '?t=');
                    let pwd = document.getElementById('password');
                    pwd?.parentNode?.insertAdjacentHTML('afterend', `<p id="captcha-section">
                        <label class="sr-only" for="captcha"><?php _e("验证码"); ?></label>
                        <input type="text" name="captcha" id="captcha" class="text-l w-100" pattern=".{4}" title="<?php _e("请输入4个字符") ?>" placeholder="<?php _e("验证码"); ?>" required />
                        <img id="captcha-img" src="<?php echo $secureUrl ?>" title="<?php _e("点击刷新") ?>" />
                    </p>`);
                    let img = document.getElementById('captcha-img');
                    let timeOut;
                    img?.addEventListener('click', function () {
                        if (img.classList.contains('not-allow')) {
                            return;
                        }
                        img.classList.add('not-allow');
                        img.src = src + Math.random();
                        timeOut = setTimeout(() => {
                            img.classList.remove('not-allow');
                        }, 1000);
                    });
                })()
            </script>
            <style>
                #captcha-section {
                    display: flex;
                }

                #captcha {
                    box-sizing: border-box;
                }

                #captcha:invalid:not(:placeholder-shown) {
                    border: 2px solid red; /* 不符合模式时显示红框 */
                }

                #captcha:valid {
                    border: 2px solid green; /* 符合模式时显示绿框 */
                }

                #captcha-img {
                    cursor: pointer;
                }

                #captcha-img.not-allow {
                    cursor: not-allowed;
                }
            </style>
            <?php
        }
    }


} 