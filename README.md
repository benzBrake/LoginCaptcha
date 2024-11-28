# LoginCaptcha

## 简介

给 Typecho 的后台登录页面增加一个验证码保护，防止恶意登录。

## 安装

1. 去 [Release](https://github.com/benzBrake/LoginCaptcha/releases/latest) 下载最新版本，解压后上传到 Typecho 插件目录。
2. 解压后确保目录名为 `LoginCaptcha`，然后目录里存在 `Plugin.php` 文件。
3. 把`LoginCaptcha`目录上传到 Typecho 的插件目录 `/usr/plugins` 下。
4. 登录 Typecho 后台，进入插件管理，启用 `LoginCaptcha` 插件。

## 功能

- 在登录页面增加验证码输入框，防止恶意登录。

## 使用

启用插件后，登录 Typecho 后台时，会自动弹出验证码输入框，输入正确的验证码后才能登录。
