<?php

namespace TypechoPlugin\LoginCaptcha;

use Typecho\Widget;
use Utils\Helper;
use Widget\ActionInterface;

class Action extends Widget implements ActionInterface
{
    public function renderCaptcha()
    {
        Helper::security()->protect();
        session_start();
        $captcha = $this->generateCaptcha();
        $_SESSION['captcha'] = $captcha['code'];
        header('Content-type: image/png');
        imagepng($captcha['image']);
        imagedestroy($captcha['image']);
        exit;
    }

    private function generateCaptcha(): array
    {
        $width = 100;
        $height = 30;
        $image = imagecreatetruecolor($width, $height);

        // Enable transparency
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Allocate a transparent background
        $background_color = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $background_color);

        // Add noise to the background with random colors
        for ($i = 0; $i < 100; $i++) {
            $noise_color = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
            imagesetpixel($image, rand(0, $width), rand(0, $height), $noise_color);
        }

        $code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4);
        $x = 10; // Starting x position

        for ($i = 0; $i < strlen($code); $i++) {
            // Generate a random color for each character
            $text_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            $angle = rand(-15, 15); // Adjusted angle range
            $y = rand(20, 25); // Adjusted Y position to fit within height

            // Create a temporary image for each character
            $temp_image = imagecreatetruecolor(20, 30);
            imagealphablending($temp_image, false);
            imagesavealpha($temp_image, true);
            imagefill($temp_image, 0, 0, $background_color);
            imagettftext($temp_image, 20, $angle, 0, $y, $text_color, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'c7a6aeea915a139782e569aaaf55a5aa.ttf', $code[$i]);

            // Copy the character to the main image
            imagecopy($image, $temp_image, $x, 0, 0, 0, 20, 30);
            $x += 25; // Increased spacing between characters
            imagedestroy($temp_image);
        }

        return array('image' => $image, 'code' => $code);
    }


    public function action()
    {

    }
}