<?php


// GENERATES THE BMP FILE FOR YOUR EPD Weather Dashboard, English text
// you need a webserver, PHP and Imagemagick installed


header("Content-Type: image/bmp");

$bat = 0;
if (isset($_GET['b'])) $bat = (float) $_GET['b'];


$weather = json_decode(file_get_contents("weather.json"), true);
$forecast = json_decode(file_get_contents("forecast.json"), true);
$uv = json_decode(file_get_contents("uv.json"), true);
$forecast_uv = json_decode(file_get_contents("forecast_uv.json"), true);



$im = imagecreatefrompng('imagini/gol.png');

$black = imagecolorallocate($im, 0, 0, 0);
$red = imagecolorallocate($im, 255, 0, 0);



if ($bat) {
    imagecopyresampled($im, imagecreatefrompng('imagini/baterie.png'), 600, 0, 0, 0, 40, 16, 50, 22);
    imagestring($im, 2, 604, 1,  $bat . 'V', ($bat < 3.4 ? $red : $black));
}

if ($uv['value'] > 5) {
    imagestring($im, 5, 400, 0,  'UV: ' . number_format($uv['value'], 2), $red);
} else {
    imagestring($im, 3, 425, 1,  'UV: ' . number_format($uv['value'], 2), $black);
}

imagecopyresampled($im, imagecreatefrompng('imagini/rasarit.png'), 486, 1, 0, 0, 16, 16, 22, 22);
imagestring($im, 3, 505, 1,  date("H:i", $weather['sys']['sunrise']), $red);
imagecopyresampled($im, imagecreatefrompng('imagini/apus.png'), 545, 1, 0, 0, 14, 14, 22, 22);
imagestring($im, 3, 562, 1,  date("H:i", $weather['sys']['sunset']), $black);

imagestring($im, 3, 425, 16, date("l, d F @ H:i", $weather['dt']), $black);




imagestring($im, 5, 1, 0,  ucfirst($weather['weather'][0]['description']), $black);
$y = 0;
imagestring($im, 5, 1, $y + 16, number_format((float) $weather['main']['temp'], 2) . chr(176) . 'C', $black);
imagestring($im, 5, 1, $y + 32, number_format((float) $weather['main']['humidity'], 0) . '% RH', $black);
imagestring($im, 5, 1, $y + 48, number_format((float)  $weather['wind']['speed'] * 3.6, 1) . ' km/h', $black);
imagestring($im, 5, 1, $y + 64, number_format((float)  $weather['main']['pressure'], 0) . ' mb', $black);

if (isset($weather['clouds']['all'])) {
    if ($weather['clouds']['all'] > 0) {
        imagestring($im, 5, 1, $y + 80, number_format((float) $weather['clouds']['all'], 0) . '% clouds', $black);
    }
}

if (isset($weather['rain']['3h'])) {
    if ($weather['rain']['3h'] > 0) {
        imagestring($im, 5, 1, $y + 96, number_format((float) $weather['rain']['3h'], 2) . ' rain', $black);
    }
} elseif (isset($weather['snow']['3h'])) {
    if ($weather['snow']['3h'] > 0.1) {
        imagestring($im, 5, 1, $y + 96, number_format((float) $weather['snow']['3h'], 2) . ' snow', $black);
    }
}

imagecopyresampled($im, imagecreatefrompng('meteo/' . $weather['weather'][0]['icon'] . '.png'), 120, 16, 0, 0, 100, 100, 256, 256);
imagecopyresampled($im, imagecreatefrompng('imagini/compas.png'), 230, 16, 0, 0, 100, 100, 862, 862);
if ((float)  $weather['wind']['deg'] > 0) {
    $deg = (float)  $weather['wind']['deg'];
    $ac = imagecreatefrompng('imagini/ac-compas.png');
    imagealphablending($ac, false);
    imagesavealpha($ac, true);
    $acr = imagerotate($ac, 360 - $deg, imageColorAllocateAlpha($ac, 0, 0, 0, 127));
    imagealphablending($acr, false);
    imagesavealpha($acr, true);
    $w = imagesx($acr);
    $h = imagesy($acr);
    $x = 280 - ($w / 2);
    $y = 66 - ($h / 2);
    imagecopy($im, $acr, $x, $y, 0, 0, $w, $h);
    imagestring($im, 5, 275, 58, number_format($deg, 0) . chr(176), $black);
} else {
    imagestring($im, 5, 275, 58, '?', $black);
}


$font = 2;
$font_h = 12;
$col = 8;
$y1 = 128;
$y2 = 258;
$x = 640 / $col;
for ($i = 0; $i < $col; $i++) {
    imagecopyresampled($im, imagecreatefrompng('meteo/' . $forecast['list'][$i]['weather'][0]['icon'] . '.png'), $x * $i + 15, $y1, 0, 0, 50, 50, 256, 256);
    imagestring($im, $font, $x * $i, $y1 + 50, '    ' . date("D H", $forecast['list'][$i]['dt']) . 'h', $black);
    imagestring($im, $font, $x * $i, $y1 + 50 + $font_h, '    ' . number_format($forecast['list'][$i]['main']['temp'], 2) . chr(176) . 'C', $black);
    imagestring($im, $font, $x * $i, $y1 + 50 + 2 * $font_h, '    ' . number_format($forecast['list'][$i]['main']['humidity'], 0) . '% RH', $black);
    imagestring($im, $font, $x * $i, $y1 + 50 + 3 * $font_h, '  ' . number_format($forecast['list'][$i]['wind']['speed'] * 3.6, 2) . ' km/h', $black);

    if (isset($forecast['list'][$i]['clouds']['all']))
        if ($forecast['list'][$i]['clouds']['all'] > 0)
            imagestring($im, $font, $x * $i, $y1 + 50 + 4 * $font_h, '    ' . number_format((float) $forecast['list'][$i]['clouds']['all'], 0) . '% cl', $black);
    if (isset($forecast['list'][$i]['rain']['3h']))
        if ($forecast['list'][$i]['rain']['3h'] > 0)
            imagestring($im, $font, $x * $i, $y1 + 50 + 5 * $font_h, '    ' . number_format((float) $forecast['list'][$i]['rain']['3h'], 2) . ' ra', $black);
    if (isset($forecast['list'][$i]['snow']['3h']))
        if ($forecast['list'][$i]['snow']['3h'] > 0.1)
            imagestring($im, $font, $x * $i, $y1 + 50 + 5 * $font_h, '    ' . number_format((float) $forecast['list'][$i]['snow']['3h'], 2) . ' sn', $black);


    imagecopyresampled($im, imagecreatefrompng('meteo/' . $forecast['list'][$i + 8]['weather'][0]['icon'] . '.png'), $x * $i + 15, $y2, 0, 0, 50, 50, 256, 256);
    imagestring($im, $font, $x * $i, $y2 + 50, '    ' . date("D H", $forecast['list'][$i + $col]['dt']) . 'h', $black);
    imagestring($im, $font, $x * $i, $y2 + 50 + $font_h, '    ' . number_format($forecast['list'][$i + $col]['main']['temp'], 2) . chr(176) . 'C', $black);
    imagestring($im, $font, $x * $i, $y2 + 50 + 2 * $font_h, '    ' . number_format($forecast['list'][$i + $col]['main']['humidity'], 0) . '% RH', $black);
    imagestring($im, $font, $x * $i, $y2 + 50 + 3 * $font_h, '  ' . number_format($forecast['list'][$i + $col]['wind']['speed'] * 3.6, 2) . ' km/h', $black);

    if (isset($forecast['list'][$i + $col]['clouds']['all']))
        if ($forecast['list'][$i + $col]['clouds']['all'] > 0)
            imagestring($im, $font, $x * $i, $y2 + 50 + 4 * $font_h, '    ' . number_format((float) $forecast['list'][$i + $col]['clouds']['all'], 0) . '% cl', $black);
    if (isset($forecast['list'][$i + $col]['rain']['3h']))
        if ($forecast['list'][$i + $col]['rain']['3h'] > 0)
            imagestring($im, $font, $x * $i, $y2 + 50 + 5 * $font_h, '    ' . number_format((float) $forecast['list'][$i + $col]['rain']['3h'], 2) . ' ra', $black);
    if (isset($forecast['list'][$i + $col]['snow']['3h']))
        if ($forecast['list'][$i + $col]['snow']['3h'] > 0.1)
            imagestring($im, $font, $x * $i, $y2 + 50 + 5 * $font_h, '    ' . number_format((float) $forecast['list'][$i + $col]['snow']['3h'], 2) . ' sn', $black);
}




imagepng($im, 'imagini/image.png');
imagedestroy($im);

// executes Imagemagick convert because GxEPD library can not display the image produced by PHP's imagebmp() command.
exec("convert imagini/image.png BMP3:imagini/image.bmp");


header("Content-Length: " . filesize('imagini/image.bmp'));
$fp = fopen('imagini/image.bmp', 'rb');
fpassthru($fp);
fclose($fp);
exit;


function file_get_contents_utf8($fn)
{
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8');
}
