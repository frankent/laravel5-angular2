<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// import the Intervention Image Manager Class


class CloudDetection extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detect:cloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dectect cloud in interested area';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        //
        function getimg($url) {
            $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
            $headers[] = 'Connection: Keep-Alive';
            $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
            $user_agent = 'php';
            $process = curl_init($url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_HEADER, 0);
            curl_setopt($process, CURLOPT_USERAGENT, $user_agent); //check here         
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
            $return = curl_exec($process);
            curl_close($process);
            return $return;
        }

        function findcloud() {
            $img_path = public_path() . "\images\image.png";
            $img = imagecreatefrompng($img_path);

// Size of interesting Area 
            $x1 = 330;
            $y1 = 13;
            $x2 = 788;
            $y2 = 280;
            $sizeAreaX = $x2 - $x1 + 1;
            $sizeAreaY = $y2 - $y1 + 1;

            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    $countAreaObj[$i][$j] = 0;
                }
            }

            $interestArea = imagecreatetruecolor($sizeAreaX, $sizeAreaY);
            $interestAreaReal = imagecreatetruecolor($sizeAreaX, $sizeAreaY);

// Thresholding RGB in interesting range
            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    // Get RGB 
                    $tmprgb = imagecolorat($img, $i + $x1, $j + $y1);
                    $r = ($tmprgb >> 16) & 0xFF;
                    $g = ($tmprgb >> 8) & 0xFF;
                    $b = $tmprgb & 0xFF;
                    $rgbR = imagecolorallocate($interestAreaReal, $r, $g, $b);
                    imagesetpixel($interestAreaReal, $i, $j, $rgbR);

                    if (($r >= 0 && $r <= 30) && ($g >= 0 && $g <= 20) && ($b >= 230 && $b <= 255)) { // Blue(-31.5)
                        $r = 0;
                        $g = 0;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 0 && $r <= 45) && ($g >= 228 && $g <= 255) && ($b >= 218 && $b <= 255)) { // Cyan(-10.0)
                        $r = 20;
                        $g = 240;
                        $b = 240;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 0 && $r <= 100) && ($g >= 200 && $g <= 255) && ($b >= 100 && $b <= 160)) { // Green(10.0)
                        $r = 12;
                        $g = 245;
                        $b = 140;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 0 && $r <= 100) && ($g >= 200 && $g <= 255) && ($b >= 0 && $b <= 80)) { // Green(13.0)
                        $r = 1;
                        $g = 255;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 0 && $r <= 100) && ($g >= 100 && $g <= 200) && ($b >= 0 && $b <= 81)) { // Green(18.0)
                        $r = 0;
                        $g = 179;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 0 && $r <= 60) && ($g >= 130 && $g <= 200) && ($b >= 0 && $b <= 180)) { // Green(23.0)
                        $r = 6;
                        $g = 156;
                        $b = 57;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 197 && $r <= 255) && ($g >= 205 && $g <= 255) && ($b >= 0 && $b <= 100)) { // Yellow(28.0)
                        $r = 247;
                        $g = 255;
                        $b = 8;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 200 && $r <= 255) && ($g >= 160 && $g <= 225) && ($b >= 0 && $b <= 75)) { // Yellow(33.0)
                        $r = 255;
                        $g = 197;
                        $b = 17;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 200 && $r <= 255) && ($g >= 130 && $g <= 220) && ($b >= 0 && $b <= 70)) { // Orange(38.0)
                        $r = 249;
                        $g = 172;
                        $b = 4;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 225 && $r <= 255) && ($g >= 50 && $g <= 121) && ($b >= 0 && $b <= 120)) { // Orange(43.0)
                        $r = 255;
                        $g = 81;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 200 && $r <= 255) && ($g >= 0 && $g <= 60) && ($b >= 0 && $b <= 65)) { // Red(48.0)
                        $r = 253;
                        $g = 0;
                        $b = 3;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 190 && $r <= 255) && ($g >= 0 && $g <= 50) && ($b >= 60 && $b <= 130)) { // Magenta(53.0)
                        $r = 255;
                        $g = 0;
                        $b = 104;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 210 && $r <= 255) && ($g >= 0 && $g <= 50) && ($b >= 210 && $b <= 255)) { // Purple(58.0)
                        $r = 255;
                        $g = 0;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 200 && $r <= 255) && ($g >= 120 && $g <= 160) && ($b >= 230 && $b <= 255)) { // Purple(63.0)
                        $r = 245;
                        $g = 135;
                        $b = 245;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 235 && $r <= 255) && ($g >= 190 && $g <= 230) && ($b >= 230 && $b <= 255)) { // Pink(68.0)
                        $r = 248;
                        $g = 205;
                        $b = 248;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 240 && $r <= 255) && ($g >= 195 && $g <= 235) && ($b >= 240 && $b <= 255)) { // Pink(73.0)
                        $r = 255;
                        $g = 230;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                    } elseif (($r >= 240 && $r <= 255) && ($g >= 240 && $g <= 255) && ($b >= 240 && $b <= 255)) { // White(78.0)
                        $r = 255;
                        $g = 255;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                    } else {
                        $r = 100;
                        $g = 100;
                        $b = 100;
                        $countAreaObj[$j][$i] = 0;
                    }
                    $rgb = imagecolorallocate($interestArea, $r, $g, $b);
                    imagesetpixel($interestArea, $i, $j, $rgb);
                }
            }

// Connected Component Analysis & Object Counting
            $numObj = 1;
            $maxPixelX[0] = 0;
            $maxPixelY[0] = 0;
            $minPixelX[0] = $sizeAreaX;
            $minPixelY[0] = $sizeAreaY;
            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    if ($i - 1 < 0) {
                        $topPx = 0;
                    } else {
                        $topPx = $countAreaObj[$i - 1][$j];
                    }
                    if ($j - 1 < 0) {
                        $leftPx = 0;
                    } else {
                        $leftPx = $countAreaObj[$i][$j - 1];
                    }
                    if ($topPx == 0 && $leftPx == 0) {
                        if ($countAreaObj[$i][$j] != 0) {
                            $maxPixelX[$numObj] = $i;
                            $maxPixelY[$numObj] = $j;
                            $minPixelX[$numObj] = $i;
                            $minPixelY[$numObj] = $j;
                            $countAreaObj[$i][$j] = $numObj++;
                        }
                    } elseif (($topPx != 0 && $leftPx == 0) || ($topPx == 0 && $leftPx != 0)) {
                        if ($countAreaObj[$i][$j] != 0) {
                            if ($topPx != 0) {
                                $countAreaObj[$i][$j] = $topPx;
                                if ($i > $maxPixelX[$countAreaObj[$i][$j]]) {
                                    $maxPixelX[$countAreaObj[$i][$j]] = $i;
                                }
                                if ($j > $maxPixelY[$countAreaObj[$i][$j]]) {
                                    $maxPixelY[$countAreaObj[$i][$j]] = $j;
                                }
                                if ($i < $minPixelX[$countAreaObj[$i][$j]]) {
                                    $minPixelX[$countAreaObj[$i][$j]] = $i;
                                }
                                if ($j < $minPixelY[$countAreaObj[$i][$j]]) {
                                    $minPixelY[$countAreaObj[$i][$j]] = $j;
                                }
                            } else {
                                $countAreaObj[$i][$j] = $leftPx;
                                if ($i > $maxPixelX[$countAreaObj[$i][$j]]) {
                                    $maxPixelX[$countAreaObj[$i][$j]] = $i;
                                }
                                if ($j > $maxPixelY[$countAreaObj[$i][$j]]) {
                                    $maxPixelY[$countAreaObj[$i][$j]] = $j;
                                }
                                if ($i < $minPixelX[$countAreaObj[$i][$j]]) {
                                    $minPixelX[$countAreaObj[$i][$j]] = $i;
                                }
                                if ($j < $minPixelY[$countAreaObj[$i][$j]]) {
                                    $minPixelY[$countAreaObj[$i][$j]] = $j;
                                }
                            }
                        }
                    } else {
                        if ($countAreaObj[$i][$j] != 0) {
                            if ($topPx != $leftPx) {
                                $countAreaObj[$i][$j] = min($topPx, $leftPx);
                                if ($topPx == $countAreaObj[$i][$j]) {
                                    $countAreaObj[$i][$j - 1] = $countAreaObj[$i][$j];
                                    if ($i > $maxPixelX[$countAreaObj[$i][$j - 1]]) {
                                        $maxPixelX[$countAreaObj[$i][$j - 1]] = $i;
                                    }
                                    if ($j > $maxPixelY[$countAreaObj[$i][$j]]) {
                                        $maxPixelY[$countAreaObj[$i][$j]] = $j;
                                    }
                                    if ($i < $minPixelX[$countAreaObj[$i][$j]]) {
                                        $minPixelX[$countAreaObj[$i][$j]] = $i;
                                    }
                                    if ($j < $minPixelY[$countAreaObj[$i][$j]]) {
                                        $minPixelY[$countAreaObj[$i][$j]] = $j;
                                    }
                                    $arrayPixel[($j + ($sizeAreaX * $i)) - 1] = $countAreaObj[$i][$j];
                                    for ($a = 0; $a <= $i; $a++) {
                                        for ($b = 0; $b <= $j; $b++) {
                                            if ($countAreaObj[$a][$b] == $leftPx) {
                                                $countAreaObj[$a][$b] = $topPx;
                                                $arrayPixel[$b + ($sizeAreaX * $a)] = $countAreaObj[$a][$b];
                                                if ($a > $maxPixelX[$countAreaObj[$a][$b]]) {
                                                    $maxPixelX[$countAreaObj[$a][$b]] = $a;
                                                }
                                                if ($b > $maxPixelY[$countAreaObj[$a][$b]]) {
                                                    $maxPixelY[$countAreaObj[$a][$b]] = $j;
                                                }
                                                if ($a < $minPixelX[$countAreaObj[$a][$b]]) {
                                                    $minPixelX[$countAreaObj[$a][$b]] = $i;
                                                }
                                                if ($b < $minPixelY[$countAreaObj[$a][$b]]) {
                                                    $minPixelY[$countAreaObj[$a][$b]] = $j;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $countAreaObj[$i - 1][$j] = $countAreaObj[$i][$j];
                                    if ($i > $maxPixelX[$countAreaObj[$i][$j - 1]]) {
                                        $maxPixelX[$countAreaObj[$i][$j - 1]] = $i;
                                    }
                                    if ($j > $maxPixelY[$countAreaObj[$i][$j]]) {
                                        $maxPixelY[$countAreaObj[$i][$j]] = $j;
                                    }
                                    if ($i < $minPixelX[$countAreaObj[$i][$j]]) {
                                        $minPixelX[$countAreaObj[$i][$j]] = $i;
                                    }
                                    if ($j < $minPixelY[$countAreaObj[$i][$j]]) {
                                        $minPixelY[$countAreaObj[$i][$j]] = $j;
                                    }
                                    $arrayPixel[$j + ($sizeAreaX * ($i - 1))] = $countAreaObj[$i][$j];
                                    for ($a = 0; $a < $i; $a++) {
                                        for ($b = 0; $b < $j; $b++) {
                                            if ($countAreaObj[$a][$b] == $topPx) {
                                                $countAreaObj[$a][$b] = $leftPx;
                                                $arrayPixel[$b + ($sizeAreaX * $a)] = $countAreaObj[$a][$b];
                                                if ($a > $maxPixelX[$countAreaObj[$a][$b]]) {
                                                    $maxPixelX[$countAreaObj[$a][$b]] = $a;
                                                }
                                                if ($b > $maxPixelY[$countAreaObj[$a][$b]]) {
                                                    $maxPixelY[$countAreaObj[$a][$b]] = $j;
                                                }
                                                if ($a < $minPixelX[$countAreaObj[$a][$b]]) {
                                                    $minPixelX[$countAreaObj[$a][$b]] = $i;
                                                }
                                                if ($b < $minPixelY[$countAreaObj[$a][$b]]) {
                                                    $minPixelY[$countAreaObj[$a][$b]] = $j;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $countAreaObj[$i][$j] = $topPx;
                                if ($i > $maxPixelX[$countAreaObj[$i][$j]]) {
                                    $maxPixelX[$countAreaObj[$i][$j]] = $i;
                                }
                                if ($j > $maxPixelY[$countAreaObj[$i][$j]]) {
                                    $maxPixelY[$countAreaObj[$i][$j]] = $j;
                                }
                                if ($i < $minPixelX[$countAreaObj[$i][$j]]) {
                                    $minPixelX[$countAreaObj[$i][$j]] = $i;
                                }
                                if ($j < $minPixelY[$countAreaObj[$i][$j]]) {
                                    $minPixelY[$countAreaObj[$i][$j]] = $j;
                                }
                            }
                        }
                    }
                    $arrayPixel[$j + ($sizeAreaX * $i)] = $countAreaObj[$i][$j];
                }
            }


// Number of Objects
            $tmplabelObj = array_unique($arrayPixel);
            $numObj = count($tmplabelObj) - 1;
            $countLabel = 0;
            foreach ($tmplabelObj as $value) {
                $labelObj[$countLabel++] = $value;
            }

// draw the Rectangle
            $red = imagecolorallocate($interestAreaReal, 255, 0, 0);
            for ($i = 1; $i <= $numObj; $i++) {
                imagerectangle($interestAreaReal, $maxPixelY[$labelObj[$i]], $maxPixelX[$labelObj[$i]], $minPixelY[$labelObj[$i]], $minPixelX[$labelObj[$i]], $red);
            }

// output image in the browser
//            imagepng($interestAreaReal);
            
            $save_path = public_path() . "\images\detect_image.png";
            imagepng($interestAreaReal,$save_path);
// free memory
            imagedestroy($interestAreaReal);
        }

        $imgurl = 'http://weather.tmd.go.th/omk/omk240_latest.png';
        $image = getimg($imgurl);
        $save_path = public_path() . "\images\image.png";
        file_put_contents($save_path, $image);
        
        findcloud();

        
    }

}
