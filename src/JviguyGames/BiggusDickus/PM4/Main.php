<?php

declare(strict_types=1);

namespace JviguyGames\BiggusDickus\PM4;

use JviguyGames\BiggusDickus\Loader;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use function file_get_contents;
use function imagecreatefrompng;

class Main implements Listener {

    /** @var Skin $skin the dick geo / skin */
    public Skin $skin;

    public function __construct(public Loader $loader){}

    public function onEnable() : void {
        $this->loader->getServer()->getLogger()->warning('LOADED ON PM4');
        $this->loader->saveResource("dildo.png");
        $this->loader->saveResource("dildo.geo.json");
        $this->loader->getServer()->getPluginManager()->registerEvents($this, $this->loader);
        $this->skin = new Skin("belly", Loader::fromImage(imagecreatefrompng($this->loader->getDataFolder() . "dildo.png")), "", "geometry.dildo", file_get_contents($this->loader->getDataFolder() . "dildo.geo.json"));
    }

    public function attachDick(Player $entity) : void {
        $entity->getNetworkProperties()->setGenericFlag(EntityMetadataProperties::RIDER_ROTATION_LOCKED, true);
        $dick = new DickEntity($entity->getLocation(), $this->skin);
        $dick->getNetworkProperties()->setLong(EntityMetadataProperties::OWNER_EID, $entity->getId());
        $entity->getWorld()->addEntity($dick);
        $dick->spawnToAll();
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $this->attachDick($event->getPlayer());
    }
}
