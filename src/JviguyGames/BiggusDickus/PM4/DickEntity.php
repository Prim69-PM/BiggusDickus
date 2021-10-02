<?php

declare(strict_types=1);

namespace JviguyGames\BiggusDickus\PM4;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

class DickEntity extends Human
{

    public float $height = 0;
    public float $width = 0;
    public $gravity = 0;
    public $canCollide = false;
    protected $drag = 0;

    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
        $this->setCanSaveWithChunk(false);
        parent::__construct($location, $skin, $nbt);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, 0.0);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0.0);
        $this->setNameTagVisible(false);
        $this->setNameTagAlwaysVisible(false);
        $this->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 2, 0));
    }

    protected function sendSpawnPacket(Player $player): void
    {
        parent::sendSpawnPacket($player);
        if ($this->getOwningEntityId() !== null) {
            $pk = new SetActorLinkPacket();
            $pk->link = new EntityLink($this->getOwningEntityId(), $this->getId(), EntityLink::TYPE_PASSENGER, true,true);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function updateProperties()
    {
        $this->setSneaking($this->getOwningEntity()->isSneaking());
        $this->setScale($this->getOwningEntity()->getScale());
        $this->setInvisible(($this->getOwningEntity()->isInvisible() or !$this->getOwningEntity()->isAlive()));
        if (!empty($this->getNetworkProperties()->getDirty())) $this->sendData($this->getViewers());
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = Entity::entityBaseTick($tickDiff);
        /** @var Player $player */
        if (($player = $this->getOwningEntity()) instanceof Player && $player->isConnected()) {
            $this->updateProperties();
            if (!($this->getPosition()->equals($player->getPosition()) && $this->getLocation()->yaw === $player->getLocation()->yaw)) {
                $this->setPositionAndRotation($player->getPosition(), $player->getLocation()->yaw, 0);
                $hasUpdate = true;
            }
        } else {
            $this->flagForDespawn();
            return true;
        }
        return $hasUpdate;
    }

    public function isFireProof() : bool { return true; }

    public function canBeCollidedWith() : bool { return false; }

    public function canCollideWith(Entity $entity) : bool { return false; }

    public function canBeMovedByCurrents() : bool { return false; }

    public function canBreathe() : bool { return true; }

    public function attack(EntityDamageEvent $source) : void {}
}