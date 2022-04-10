<?php

namespace wcf\system\event\listener;

use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncDeleteBackgroundJob;

class MinecraftSyncUserDeleteListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        if ($eventObj->action == 'delete') {
            /** @var BackgroundQueueHandler */
            $handler = BackgroundQueueHandler::getInstance();
            foreach ($eventObj->getObjects() as $userEditor) {
                $handler->enqueueIn(new MinecraftSyncDeleteBackgroundJob($userEditor->userID));
            }
        }
    }
}
