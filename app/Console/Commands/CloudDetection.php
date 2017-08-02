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

        function findcloud_png(&$detectCloud, &$perArea, $type) {


            if ($type == IMAGETYPE_PNG) {
                //echo 'The picture is  a png';
                $img_path = public_path() . "/images/radar/raws/image.png";
                $img = imagecreatefrompng($img_path);
                $typeImg = 0;
            } else if ($type == IMAGETYPE_GIF) {
                //echo 'The picture is  a gif';
                $img_path = public_path() . "/images/radar/raws/image.gif";
                $img = imagecreatefromgif($img_path);
                $typeImg = 1;
            }

// Size of interesting Area 
            $x1 = 574;
            $y1 = 23;
            $x2 = 588;
            $y2 = 37;
            $sizeAreaX = $x2 - $x1 + 1;
            $sizeAreaY = $y2 - $y1 + 1;

            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    $countAreaObj[$i][$j] = 0;
                }
            }

            $interestArea = imagecreatetruecolor($sizeAreaX, $sizeAreaY);
            $interestAreaReal = imagecreatetruecolor($sizeAreaX, $sizeAreaY);

//Detect Cloud 
            $detectCloud = false;
            $perArea = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

// Thresholding RGB in interesting range
            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    // Get RGB 
                    if ($typeImg == 0) {
                        $tmprgb = imagecolorat($img, $i + $x1, $j + $y1);
                        $r = ($tmprgb >> 16) & 0xFF;
                        $g = ($tmprgb >> 8) & 0xFF;
                        $b = $tmprgb & 0xFF;
                        $rgbR = imagecolorallocate($interestAreaReal, $r, $g, $b);
                    } else {
                        $tmprgb = imagecolorat($img, $i + $x1, $j + $y1);
                        $color_tran = imagecolorsforindex($img, $tmprgb);
                        $r = $color_tran["red"];
                        $g = $color_tran["green"];
                        $b = $color_tran["blue"];
                        $rgbR = imagecolorallocate($interestAreaReal, $r, $g, $b);
                    }

                    imagesetpixel($interestAreaReal, $i, $j, $rgbR);

                    if (($r >= 0 && $r <= 0) && ($g >= 0 && $g <= 0) && ($b >= 255 && $b <= 255)) { // Blue(-31.5)
                        $r = 0;
                        $g = 0;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[0] ++;
                    } elseif (($r >= 0 && $r <= 0) && ($g >= 255 && $g <= 255) && ($b >= 255 && $b <= 255)) { // Cyan(-10.0)
                        $r = 0;
                        $g = 255;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[1] ++;
                    } elseif (($r >= 0 && $r <= 0) && ($g >= 255 && $g <= 255) && ($b >= 128 && $b <= 128)) { // Green(10.0)
                        $r = 0;
                        $g = 255;
                        $b = 128;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[2] ++;
                    } elseif (($r >= 0 && $r <= 0) && ($g >= 255 && $g <= 255) && ($b >= 0 && $b <= 0)) { // Green(13.0)
                        $r = 0;
                        $g = 255;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[3] ++;
                    } elseif (($r >= 0 && $r <= 0) && ($g >= 175 && $g <= 175) && ($b >= 0 && $b <= 0)) { // Green(18.0)
                        $r = 0;
                        $g = 175;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[4] ++;
                    } elseif ((($r >= 0 && $r <= 0) && ($g >= 150 && $g <= 150) && ($b >= 50 && $b <= 50))) { // Green(23.0)
                        $r = 0;
                        $g = 150;
                        $b = 50;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[5] ++;
                    } elseif ((($r >= 255 && $r <= 255) && ($g >= 255 && $g <= 255) && ($b >= 0 && $b <= 0))) { // Yellow(28.0)
                        $r = 255;
                        $g = 255;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[6] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 200 && $g <= 200) && ($b >= 0 && $b <= 0)) { // Yellow(33.0)
                        $r = 255;
                        $g = 200;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[7] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 170 && $g <= 170) && ($b >= 0 && $b <= 0)) { // Orange(38.0)
                        $r = 255;
                        $g = 170;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[8] ++;
                    } elseif (($r >= 225 && $r <= 255) && ($g >= 85 && $g <= 85) && ($b >= 0 && $b <= 0)) { // Orange(43.0)
                        $r = 255;
                        $g = 85;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[9] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 0 && $g <= 0) && ($b >= 0 && $b <= 0)) { // Red(48.0)
                        $r = 253;
                        $g = 0;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[10] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 0 && $g <= 0) && ($b >= 100 && $b <= 100)) { // Magenta(53.0)
                        $r = 255;
                        $g = 0;
                        $b = 100;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[11] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 0 && $g <= 0) && ($b >= 255 && $b <= 255)) { // Purple(58.0)
                        $r = 255;
                        $g = 0;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[12] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 128 && $g <= 128) && ($b >= 255 && $b <= 255)) { // Purple(63.0)
                        $r = 255;
                        $g = 128;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[13] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 200 && $g <= 200) && ($b >= 255 && $b <= 255)) { // Pink(68.0)
                        $r = 255;
                        $g = 200;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[14] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 225 && $g <= 225) && ($b >= 255 && $b <= 255)) { // Pink(73.0)
                        $r = 255;
                        $g = 225;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[15] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 255 && $g <= 255) && ($b >= 255 && $b <= 255)) { // White(78.0)
                        $r = 255;
                        $g = 255;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[16] ++;
                    } else {
                        $r = 0;
                        $g = 0;
                        $b = 0;
                        $countAreaObj[$j][$i] = 0;
                    }
                    $rgb = imagecolorallocate($interestArea, $r, $g, $b);
                    imagesetpixel($interestArea, $i, $j, $rgb);
                }
            }
            $black = imagecolorallocate($interestArea, 0, 0, 0);
            imagecolortransparent($interestArea, $black);

            $sizePixelArea = $sizeAreaX * $sizeAreaY;
            /* for ($i=0; $i < 17; $i++) { 
              $perArea[$i] = $perArea[$i] / $sizePixelArea;
              echo $perArea[$i];
              } */
//echo $detectCloud;
//echo $sizePixelArea;
// Connected Component Analysis & Object Counting
            $numObj = 1;
            $maxPixelX[0] = 0;
            $maxPixelY[0] = 0;
            $minPixelX[0] = $sizeAreaX;
            $minPixelY[0] = $sizeAreaY;
            for ($i = 0; $i < $sizeAreaY; $i++) {
                for ($j = 0; $j < $sizeAreaX; $j++) {
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




            if ($detectCloud) {

                $date = new \DateTime();
                $result = $date->format('Y-m-d_H-i-s');
                $save_path = public_path() . "/images/radar/interested/" . "detect_image_" . $result;
                $save_path_cloud = public_path() . "/images/radar/cloud/" . "cloud_image_" . $result;

                if ($typeImg == 0) {
                    $save_path = $save_path . ".png";
                    $save_path_cloud = $save_path_cloud . ".png";
                    imagepng($interestAreaReal, $save_path);
                    imagepng($interestArea, $save_path_cloud);
                } else {
                    $save_path = $save_path . ".gif";
                    $save_path_cloud = $save_path_cloud . ".gif";
                    imagegif($interestAreaReal, $save_path);
                    imagegif($interestArea, $save_path_cloud);
                }
            }
// free memory
            imagedestroy($interestAreaReal);
        }

        function findcloud_gif(&$detectCloud, &$perArea, $type) {


            if ($type == IMAGETYPE_PNG) {
                //echo 'The picture is  a png';
                $img_path = public_path() . "/images/radar/raws/image.png";
                $img = imagecreatefrompng($img_path);
                $typeImg = 0;
            } else if ($type == IMAGETYPE_GIF) {
                //echo 'The picture is  a gif';
                $img_path = public_path() . "/images/radar/raws/image.gif";
                $img = imagecreatefromgif($img_path);
                $typeImg = 1;
            }

/// Size of interesting Area 
            $x1 = 574;
            $y1 = 23;
            $x2 = 588;
            $y2 = 37;
            $sizeAreaX = $x2 - $x1 + 1;
            $sizeAreaY = $y2 - $y1 + 1;

            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    $countAreaObj[$i][$j] = 0;
                }
            }

            $interestArea = imagecreatetruecolor($sizeAreaX, $sizeAreaY);
            $interestAreaReal = imagecreatetruecolor($sizeAreaX, $sizeAreaY);

//Detect Cloud 
            $detectCloud = false;
            $perArea = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

// Thresholding RGB in interesting range
            for ($i = 0; $i < $sizeAreaX; $i++) {
                for ($j = 0; $j < $sizeAreaY; $j++) {
                    // Get RGB 
                    if ($typeImg == 0) {
                        $tmprgb = imagecolorat($img, $i + $x1, $j + $y1);
                        $r = ($tmprgb >> 16) & 0xFF;
                        $g = ($tmprgb >> 8) & 0xFF;
                        $b = $tmprgb & 0xFF;
                        $rgbR = imagecolorallocate($interestAreaReal, $r, $g, $b);
                    } else {
                        $tmprgb = imagecolorat($img, $i + $x1, $j + $y1);
                        $color_tran = imagecolorsforindex($img, $tmprgb);
                        $r = $color_tran["red"];
                        $g = $color_tran["green"];
                        $b = $color_tran["blue"];
                        $rgbR = imagecolorallocate($interestAreaReal, $r, $g, $b);
                    }

                    imagesetpixel($interestAreaReal, $i, $j, $rgbR);

                    if (($r >= 0 && $r <= 46) && ($g >= 0 && $g <= 54) && ($b >= 198 && $b <= 255)) { // Blue(-31.5)
                        $r = 0;
                        $g = 0;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[0] ++;
                    } elseif (($r >= 0 && $r <= 44) && ($g >= 212 && $g <= 255) && ($b >= 212 && $b <= 255)) { // Cyan(-10.0)
                        $r = 0;
                        $g = 255;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[1] ++;
                    } elseif (($r >= 0 && $r <= 64) && ($g >= 192 && $g <= 255) && ($b >= 68 && $b <= 192)) { // Green(10.0)
                        $r = 0;
                        $g = 255;
                        $b = 128;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[2] ++;
                    } elseif (($r >= 0 && $r <= 44) && ($g >= 212 && $g <= 255) && ($b >= 0 && $b <= 44)) { // Green(13.0)
                        $r = 0;
                        $g = 255;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[3] ++;
                    } elseif (($r >= 0 && $r <= 60) && ($g >= 100 && $g <= 175) && ($b >= 0 && $b <= 60)) { // Green(18.0)
                        $r = 0;
                        $g = 175;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[4] ++;
                    } elseif ((($r >= 0 && $r <= 28) && ($g >= 128 && $g <= 170) && ($b >= 30 && $b <= 72))) { // Green(23.0)
                        $r = 0;
                        $g = 150;
                        $b = 50;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[5] ++;
                    } elseif ((($r >= 212 && $r <= 255) && ($g >= 190 && $g <= 255) && ($b >= 0 && $b <= 44))) { // Yellow(28.0)
                        $r = 255;
                        $g = 255;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[6] ++;
                    } elseif (($r >= 215 && $r <= 255) && ($g >= 160 && $g <= 240) && ($b >= 0 && $b <= 40)) { // Yellow(33.0)
                        $r = 255;
                        $g = 200;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[7] ++;
                    } elseif (($r >= 221 && $r <= 255) && ($g >= 130 && $g <= 212) && ($b >= 0 && $b <= 44)) { // Orange(38.0)
                        $r = 255;
                        $g = 170;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[8] ++;
                    } elseif (($r >= 209 && $r <= 255) && ($g >= 42 && $g <= 125) && ($b >= 0 && $b <= 45)) { // Orange(43.0)
                        $r = 255;
                        $g = 85;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[9] ++;
                    } elseif (($r >= 202 && $r <= 255) && ($g >= 0 && $g <= 44) && ($b >= 0 && $b <= 91)) { // Red(48.0)
                        $r = 253;
                        $g = 0;
                        $b = 0;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[10] ++;
                    } elseif (($r >= 215 && $r <= 255) && ($g >= 0 && $g <= 40) && ($b >= 60 && $b <= 140)) { // Magenta(53.0)
                        $r = 255;
                        $g = 0;
                        $b = 100;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[11] ++;
                    } elseif (($r >= 212 && $r <= 255) && ($g >= 0 && $g <= 44) && ($b >= 212 && $b <= 255)) { // Purple(58.0)
                        $r = 255;
                        $g = 0;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[12] ++;
                    } elseif (($r >= 212 && $r <= 255) && ($g >= 88 && $g <= 172) && ($b >= 212 && $b <= 255)) { // Purple(63.0)
                        $r = 255;
                        $g = 128;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[13] ++;
                    } elseif (($r >= 212 && $r <= 255) && ($g >= 160 && $g <= 244) && ($b >= 212 && $b <= 255)) { // Pink(68.0)
                        $r = 255;
                        $g = 200;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[14] ++;
                    } elseif (($r >= 212 && $r <= 255) && ($g >= 185 && $g <= 268) && ($b >= 212 && $b <= 255)) { // Pink(73.0)
                        $r = 255;
                        $g = 225;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[15] ++;
                    } elseif (($r >= 255 && $r <= 255) && ($g >= 255 && $g <= 255) && ($b >= 255 && $b <= 255)) { // White(78.0)
                        $r = 255;
                        $g = 255;
                        $b = 255;
                        $countAreaObj[$j][$i] = 1;
                        $detectCloud = true;
                        $perArea[16] ++;
                    } else {
                        $r = 0;
                        $g = 0;
                        $b = 0;
                        $countAreaObj[$j][$i] = 0;
                    }
                    $rgb = imagecolorallocate($interestArea, $r, $g, $b);
                    imagesetpixel($interestArea, $i, $j, $rgb);
                }
            }
            $black = imagecolorallocate($interestArea, 0, 0, 0);
            imagecolortransparent($interestArea, $black);

            $sizePixelArea = $sizeAreaX * $sizeAreaY;
            for ($i = 0; $i < 17; $i++) {
                $perArea[$i] = $perArea[$i] / $sizePixelArea;
                //echo $perArea[$i];
            }
//echo $detectCloud;
//echo $sizePixelArea;
// Connected Component Analysis & Object Counting
            $numObj = 1;
            $maxPixelX[0] = 0;
            $maxPixelY[0] = 0;
            $minPixelX[0] = $sizeAreaX;
            $minPixelY[0] = $sizeAreaY;
            for ($i = 0; $i < $sizeAreaY; $i++) {
                for ($j = 0; $j < $sizeAreaX; $j++) {
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




            if ($detectCloud) {

                $date = new \DateTime();
                $result = $date->format('Y-m-d_H-i-s');
                $save_path = public_path() . "/images/radar/interested/" . "detect_image_" . $result;
                $save_path_cloud = public_path() . "/images/radar/cloud/" . "cloud_image_" . $result;

                if ($typeImg == 0) {
                    $save_path = $save_path . ".png";
                    $save_path_cloud = $save_path_cloud . ".png";
                    imagepng($interestAreaReal, $save_path);
                    imagepng($interestArea, $save_path_cloud);
                } else {
                    $save_path = $save_path . ".gif";
                    $save_path_cloud = $save_path_cloud . ".gif";
                    imagegif($interestAreaReal, $save_path);
                    imagegif($interestArea, $save_path_cloud);
                }
            }
// free memory
            imagedestroy($interestAreaReal);
        }

        $date = new \DateTime();
        $result = $date->format('Y-m-d_H-i-s');

        //detect cloud from image type png

        $imgurl_png = 'http://weather.tmd.go.th/omk/omk240_latest.png';
        $image_png = getimg($imgurl_png);
        $save_path_png = public_path() . "/images/radar/raws/image.png";
        file_put_contents($save_path_png, $image_png);
        $this->info($result . ":weather.tmd.go.th:imagetype" . exif_imagetype($save_path_png));
        if (exif_imagetype($save_path_png) == IMAGETYPE_PNG) {


            $detectCloud_png = false;
            $perArea_png = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


            findcloud_png($detectCloud_png, $perArea_png, IMAGETYPE_PNG);

            if ($detectCloud_png) {

                $save_path_png = public_path() . "/images/radar/original/" . "image_" . $result . ".png";
                file_put_contents($save_path_png, $image_png);
            }
        }

        //detect cloud from image type gif

        $imgurl_gif = 'http://tiwrm.haii.or.th/TyphoonTracking/rainMaker/OKI/rm_OKI_lastest.gif';
        $image_gif = getimg($imgurl_gif);
        $save_path_gif = public_path() . "/images/radar/raws/image.gif";
        file_put_contents($save_path_gif, $image_gif);
        $this->info($result . ":tiwrm.haii.or.th:imagetype" . exif_imagetype($save_path_gif));
        if (exif_imagetype($save_path_gif) == IMAGETYPE_GIF) {


            $detectCloud_gif = false;
            $perArea_gif = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

            findcloud_gif($detectCloud_gif, $perArea_gif, IMAGETYPE_GIF);

            if ($detectCloud_gif) {

                $save_path_gif = public_path() . "/images/radar/original/" . "image_" . $result . ".gif";
                file_put_contents($save_path_gif, $image_gif);
            }
        }
    }

}
