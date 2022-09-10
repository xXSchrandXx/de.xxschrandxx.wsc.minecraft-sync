<?php

namespace wcf\data\user\group\minecraft;

use wcf\data\DatabaseObject;

/**
 * MinecraftGroup Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Group\Minecraft
 */
class MinecraftGroup extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'minecraft_group';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'minecraftGroupID';

    /**
     * Returns group name
     * @return ?string
     */
    public function getGroupName()
    {
        return $this->minecraftName;
    }

    /**
     * Returns weather the user should have this group on minecraft server
     * @return ?bool
     */
    public function getShouldHave()
    {
        return $this->shouldHave;
    }

    /**
     * Returns minecraftID
     * @return ?int
     */
    public function getMinecraftID()
    {
        return $this->minecraftID;
    }


    /**
     * Returns groupID
     * @return ?int
     */
    public function getGroupID()
    {
        return $this->groupID;
    }
}
