<?php

namespace wcf\system\event\listener;

use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncDeleteBackgroundJob;
use wcf\system\background\job\MinecraftSyncSyncBackgroundJob;

class MinecraftSyncMinecraftLinkerListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        $action = $eventObj->getActionName();
        if ($action == 'create') {
            /** @var MinecraftUser */
            $minecraftUser = $eventObj->parameters['data'];
            BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncSyncBackgroundJob($minecraftUser->userID));
        } else if ($action == 'delete') {
            foreach ($eventObj->getObjects() as /** @var MinecraftUser */$minecraftUser) {
                BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncDeleteBackgroundJob($minecraftUser));
            }
        }
    }
}
