<?php

declare(strict_types=1);

namespace JviguyGames\BiggusDickus\PM3;

use JviguyGames\BiggusDickus\Loader;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use JviguyGames\BiggusDickus\PM3\DickEntity;
use pocketmine\event\player\PlayerJoinEvent;
use function file_get_contents;
use function imagecreatefrompng;

class Main implements Listener {

    /** @var Skin $skin */
    public $skin;

    public function __construct(public Loader $loader){
    }

    public function onEnable() : void {
        $this->loader->saveResource("dildo.png");
        $this->loader->saveResource("dildo.geo.json");
        Entity::registerEntity(DickEntity::class, true);
        $this->loader->getServer()->getPluginManager()->registerEvents($this, $this->loader);
        $this->skin = new Skin("belly", Loader::fromImage(imagecreatefrompng($this->loader->getDataFolder() . "dildo.png")), "", "geometry.dildo", file_get_contents($this->loader->getDataFolder() . "dildo.geo.json"));
    }

    public function attachDick(Entity $entity) : void {
        $entity->setGenericFlag(Entity::DATA_RIDER_ROTATION_LOCKED, true);
        $dick = new DickEntity($this->skin, $entity->getLevel(), Entity::createBaseNBT($entity->asVector3(), null, $entity->getYaw(), $entity->getPitch()));
        $dick->getDataPropertyManager()->setLong(Entity::DATA_OWNER_EID, $entity->getId());
        $entity->getLevel()->addEntity($dick);
        $dick->spawnToAll();
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $this->attachDick($event->getPlayer());
    }
}
