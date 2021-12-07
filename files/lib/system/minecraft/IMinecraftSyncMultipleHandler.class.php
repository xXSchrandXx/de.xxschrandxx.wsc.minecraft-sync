<?php

namespace wcf\system\minecraft;

interface IMinecraftSyncMultipleHandler
{
    /**
     * Returns the MinecraftSyncSingleHandler.
     * @param $minecraftID
     */
    public function getHandler(int $minecraftID);
}
