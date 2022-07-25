<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;

class MinecraftSyncBrandingListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        WCF::getTPL()->assign([
            'showMinecraftSyncBranding' => true
        ]);
    }
}
