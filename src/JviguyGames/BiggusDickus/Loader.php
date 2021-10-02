<?php

declare(strict_types=1);

namespace JviguyGames\BiggusDickus;

use Exception;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use function chr;
use function extension_loaded;
use function imagecolorat;
use function imagedestroy;
use function imagesx;
use function imagesy;

class Loader extends PluginBase {

    /**
     * @throws Exception
     */
    public function onEnable() : void {
        if(!extension_loaded("gd")) {
            throw new PluginException("GD library is not enabled! Please uncomment gd2 in php.ini!");
        }
        if(class_exists('pocketmine\VersionInfo')){
            $main = new PM4\Main($this);
        } elseif(\pocketmine\BASE_VERSION[0] === '3'){
            $main = new PM3\Main($this);
        } else {
            throw new Exception('sexpu');
        }
        $main->onEnable();
    }

    public static function fromImage($img) : string {
        $bytes = '';
        for ($y = 0; $y < imagesy($img); $y++) {
            for ($x = 0; $x < imagesx($img); $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $a = ((~($rgba >> 24)) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        imagedestroy($img);
        return $bytes;
    }
}
