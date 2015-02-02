<?php
/**
 * This file generates a captcha string, writes it into the $_SESSION['captcha']
 * and renders a fresh captcha graphic file to the browser.
 *
 * In the views you can use this by saying:
 * <img src="tools/showCaptcha.php" />
 *
 * Check if the typed captcha is correct by saying:
 * if ($_POST["captcha"] == $_SESSION['captcha']) { ... } else { ... }
 */

// check if php gd extension is loaded
//if (!extension_loaded('gd')) {
//    die("It looks like GD is not installed");
//}

class Captcha
{

    const LENGTH = 6;
    const CHARSET = 'ABCDEFGHJKLMNPRTUVWXYZ2346789';
    const FONT = "/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf";

    public $captcha;

    public function __call($func, $args)
    {
        $this -> show();
    }

    public function show()
    {
        $charset = self::CHARSET;
        for ($i = 0; $i < self::LENGTH; $i++) {
            do {
                $ipos = rand(0, strlen($charset) - 1);
                // checks that each letter is used only once
            } while (stripos($str_captcha, $charset[$ipos]) !== false);

            $str_captcha .= $charset[$ipos];
        }

        $_SESSION['captcha_code'] = $str_captcha;

        $im = imagecreatetruecolor(250, 70);
        $bg = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $bg);
        for ($i = 0; $i < self::LENGTH; $i++) {
            $text_color = imagecolorallocate($im, rand(0, 100), rand(10, 100), rand(0, 100));
            imagefttext($im, 35, rand(-10, 10), 20 + ($i * 30) + rand(-5, +5), 35 + rand(10, 30), $text_color, self::FONT, $str_captcha[$i]);
        }

        header('Content-type: image/png');
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, proxy-revalidate');

        imagepng($im);
        imagedestroy($im);
    }

    public static function check($captcha)
    {
        return $captcha == $_SESSION['captcha_code'];
    }

}
