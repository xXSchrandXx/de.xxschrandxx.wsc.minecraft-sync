<?php

namespace wcf\system\minecraft;

abstract class MinecraftSyncMulipleHandler extends AbstractMultipleMinecraftHandler implements IMinecraftSyncHandler
{

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (MINECRAFT_LINKER_ENABLED && MINECRAFT_SYNC_ENABLED) {
            $this->minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_SYNC_IDENTITY));
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
        foreach (getMinecrafts() as &$minecraft) {
            $handler = getHandler($minecraft);
            $tmpGroups = $handler->getGroups();
            if (!empty($tmpGroups)) {
                $this->groups = $this->groups . [$minecraft->minecraftID => $tmpGroups];
            }
        }
        return $this->groups;
    }

    /**
     * @inheritDoc
     */
    public function getHandler(minecraftsyncsinglehandler $mc)
    {
        return new MinecraftSyncSingleHandler($mc);
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

    public function sync($minecraftUUID)
    {
        foreach (getMinecrafts() as &$minecraft) {

        }
    }
}
