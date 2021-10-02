<?php

declare(strict_types=1);

namespace JviguyGames\BiggusDickus\PM3;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;

class DickEntity extends Human
{

    public $height = 0;
    public $width = 0;
    public $gravity = 0;
    public $canCollide = false;
    protected $drag = 0;

    public function __construct(Skin $skin, Level $level, CompoundTag $nbt)
    {
        $this->setCanSaveWithChunk(false);
        $this->setSkin($skin);
        parent::__construct($level, $nbt);
        $this->getDataPropertyManager()->setFloat(self::DATA_BOUNDING_BOX_WIDTH, 0.0);
        $this->getDataPropertyManager()->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, 0.0);
        $this->setNameTagVisible(false);
        $this->setNameTagAlwaysVisible(false);
        $this->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0,2,0));
    }

    protected function sendSpawnPacket(Player $player): void
    {
        parent::sendSpawnPacket($player);
        if ($this->getOwningEntityId() !== null) {
            $pk = new SetActorLinkPacket();
            $pk->link = new EntityLink($this->getOwningEntityId(), $this->getId(), EntityLink::TYPE_PASSENGER, true,true);
            $player->sendDataPacket($pk);
        }
    }

    public function updateProperties()
    {
        $this->setSneaking($this->getOwningEntity()->isSneaking());
        $this->setScale($this->getOwningEntity()->getScale());
        $this->setInvisible(($this->getOwningEntity()->isInvisible() or !$this->getOwningEntity()->isAlive()));
        if (!empty($this->getDataPropertyManager()->getDirty())) $this->sendData($this->getViewers());
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = Entity::entityBaseTick($tickDiff);
        /** @var Player $player */
        if (($player = $this->getOwningEntity()) instanceof Player && $player->isConnected()) {
            $this->updateProperties();
            if (!($this->asPosition()->equals($player->asPosition()) && $this->yaw === $player->yaw)) {
                $this->setPositionAndRotation($player, $player->getYaw(), 0);
                $hasUpdate = true;
            }
        } else {
            $this->flagForDespawn();
            return true;
        }
        return $hasUpdate;
    }

    public function isFireProof(): bool
    {
        return true;
    }

    public function canBeCollidedWith(): bool
    {
        return false;
    }

    protected function checkBlockCollision(): void
    {
    }

    public function canCollideWith(Entity $entity): bool
    {
        return false;
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    public function canBreathe(): bool
    {
        return true;
    }

    protected function applyGravity(): void
    {
    }

    public function attack(EntityDamageEvent $source): void
    {
    }
}