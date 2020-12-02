<?php

declare(strict_types=1);

namespace JviguyGames\BiggusDickus;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;

class Main extends PluginBase implements Listener {

    /** @var Skin $skin the dick geo / skin */
    public $skin;

    public function onEnable()
    {
        if (!extension_loaded("gd")) {
            throw new PluginException("GD library is not enabled! Please uncomment gd2 in php.ini!");
        }
        $this->saveResource("dildo.png");
        $this->saveResource("dildo.geo.json");
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->skin = new Skin("belly", self::fromImage(imagecreatefrompng($this->getDataFolder() . "dildo.png")), "", "geometry.dildo", file_get_contents($this->getDataFolder() . "dildo.geo.json"));
    }

    public function AttachDick(Player $player) {
        $player->setGenericFlag(Entity::DATA_RIDER_ROTATION_LOCKED, true);
        $nbt = Entity::createBaseNBT($player, null, $player->getYaw());
        $dick = new DickEntity($this->skin,$player->getLevel(),$nbt);
        $dick->getDataPropertyManager()->setLong(Human::DATA_OWNER_EID, $player->getId());
        $player->getLevel()->addEntity($dick);
        $dick->spawnToAll();
    }

    public static function fromImage($img)
    {
        $bytes = '';
        for ($y = 0; $y < imagesy($img); $y++) {
            for ($x = 0; $x < imagesx($img); $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $this->AttachDick($event->getPlayer());
    }
}
