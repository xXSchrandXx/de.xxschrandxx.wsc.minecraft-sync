<?php

namespace wcf\system\event\listener;

use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncSyncUserBackgroundJob;

class MinecraftSyncUserChangeGroupListener implements IParameterizedEventListener
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
        if ($action == 'removeFromGroups' || $action == 'addToGroups') {
            /** @var BackgroundQueueHandler */
            $handler = BackgroundQueueHandler::getInstance();
            foreach ($eventObj->getObjects() as $userEditor) {
                $handler->enqueueIn(new MinecraftSyncSyncUserBackgroundJob($userEditor->userID));
            }
        }
    }
}
