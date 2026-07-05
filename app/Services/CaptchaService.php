<?php

namespace App\Services;

class CaptchaService
{
    /**
     * Generates a CAPTCHA image and stores the code in the session.
     */
    public static function generate()
    {
        // 1. Generate random code (excluding ambiguous characters like 0, O, 1, l)
        $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $code = '';
        $length = 5;
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Store lowercase in session for case-insensitive comparison
        session()->put('captcha_code', strtolower($code));
        
        // 2. Create the image canvas
        $width = 130;
        $height = 45;
        $image = imagecreatetruecolor($width, $height);
        
        // Red, White, Black theme colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $red = imagecolorallocate($image, 220, 20, 60); // Crimson red
        $grey = imagecolorallocate($image, 200, 200, 200);
        
        // Fill background with white
        imagefill($image, 0, 0, $white);
        
        // Add random pixel noise
        for ($i = 0; $i < 250; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $grey);
        }
        
        // Add random lines (Red and Black)
        for ($i = 0; $i < 5; $i++) {
            $lineColor = (rand(0, 1) === 0) ? $red : $black;
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }
        
        // Draw the characters with slight random displacement and shadows
        for ($i = 0; $i < strlen($code); $i++) {
            $x = 15 + ($i * 20) + rand(-3, 3);
            $y = 12 + rand(-4, 4);
            
            // Draw black shadow
            imagestring($image, 5, $x + 1, $y + 1, $code[$i], $black);
            // Draw red main letter
            imagestring($image, 5, $x, $y, $code[$i], $red);
        }
        
        // Capture image output
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Verifies the user's input against the CAPTCHA code stored in the session.
     */
    public static function check($code)
    {
        $sessionCode = session()->get('captcha_code');
        // Forget captcha immediately to prevent reuse (anti-replay)
        session()->forget('captcha_code');
        return $sessionCode && strtolower($code) === $sessionCode;
    }
}
