<?php

namespace wcf\system\minecraft;

use wcf\util\StringUtil;

class MinecraftSyncMultipleHandler extends AbstractMultipleMinecraftHandler implements IMinecraftSyncMultipleHandler
{

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (MINECRAFT_SYNC_ENABLED && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY) {
            $this->minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_LINKER_IDENTITY));
        }

        parent::init();
    }

    /** @var array */
    protected $groups = [];

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        if (!empty($this->groups)) {
            return $this->groups;
        }
        foreach ($this->getMinecrafts() as &$minecraft) {
            $handler = $this->getHandler($minecraft->minecraftID);
            $tmpGroups = $handler->getGroups();
            if (!empty($tmpGroups)) {
                $this->groups .= [$minecraft->minecraftID => $tmpGroups];
            }
        }
        return $this->groups;
    }

    /**
     * @inheritDoc
     */
    public function getPlayerGroups(string $uuid)
    {
        $playerGroups = [];
        foreach ($this->getMinecrafts() as &$minecraft) {
            $handler = $this->getHandler($minecraft->minecraftID);
            $tmpGroups = $handler->getPlayerGroups($uuid);
            if (!empty($tmpGroups)) {
                $playerGroups .= [$minecraft->minecraftID => $tmpGroups];
            }
        }
        return $playerGroups;
    }

    /**
     * @inheritDoc
     */
    public function addPlayerToGroup(string $uuid, string $group)
    {
        $responses = [];
        foreach ($this->getMinecrafts() as &$minecraft) {
            $handler = $this->getHandler($minecraft->minecraftID);
            $tmpResponse = $handler->addPlayerToGroup($uuid, $group);
            if (!empty($tmpGroups)) {
                $responses .= [$minecraft->minecraftID => $tmpResponse];
            }
        }
        return $responses;
    }

    /**
     * @inheritDoc
     */
    public function removePlayerFromGroup(string $uuid, string $group)
    {
        $responses = [];
        foreach ($this->getMinecrafts() as &$minecraft) {
            $handler = $this->getHandler($minecraft->minecraftID);
            $tmpResponse = $handler->removePlayerFromGroup($uuid, $group);
            if (!empty($tmpGroups)) {
                $responses .= [$minecraft->minecraftID => $tmpResponse];
            }
        }
        return $responses;
    }

    /**
     * @inheritDoc
     */
    public function getHandler(int $minecraftID)
    {
        return new MinecraftSyncSingleHandler($minecraftID);
    }

    public function syncUser($user)
    {
        $minecraftList = new wcf\data\user\minecraft\MinecraftList();
        $minecraftList->getConditionBuilder()->add('userID = ?', [$user->userID]);
        $minecraftList->readObjects();

        $minecraftObjects = $minecraftList->getObjects();

        if (empty($minecraftObjects)) {
            return;
        }
        foreach ($minecraftObjects as &$minecraftObject) {
            sync($minecraftObject->minecraftUUID);
        }
    }

    /**
     * @inheritDoc
     */
    public function sync($minecraftUUID)
    {
        $responses = [];
        foreach (getMinecrafts() as &$minecraft) {
            $handler = $this->getHandler($minecraft->minecraftID);
            $responses .= [$minecraft => $handler->sync($minecraftUUID)];
        }
        return $responses;
    }
}
