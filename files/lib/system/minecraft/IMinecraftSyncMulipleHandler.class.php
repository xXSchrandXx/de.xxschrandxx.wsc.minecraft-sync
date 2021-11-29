<?php

namespace wcf\system\minecraft;

interface IMinecraftSyncMulipleHandler
{
    /**
     * Returns the MinecraftSyncSingleHandler.
     * @param $mc MinecraftSyncSingleHandler
     */
    public function getHandler(minecraftsyncsinglehandler $mc);
}
