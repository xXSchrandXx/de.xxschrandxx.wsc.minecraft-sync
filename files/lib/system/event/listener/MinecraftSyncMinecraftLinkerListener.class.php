<?php

namespace wcf\system\event\listener;

use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncDeleteBackgroundJob;
use wcf\system\background\job\MinecraftSyncSyncUserBackgroundJob;

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
            $minecraftUser = $eventObj->getParameters()['data'];
            $job = new MinecraftSyncSyncUserBackgroundJob($minecraftUser['userID']);
//            $job->perform();
            BackgroundQueueHandler::getInstance()->enqueueIn($job);
        } else if ($action == 'delete') {
            foreach ($eventObj->getObjects() as $minecraftUser) {
                $job = new MinecraftSyncDeleteBackgroundJob($minecraftUser->getDecoratedObject());
//                $job->perform();
                BackgroundQueueHandler::getInstance()->enqueueIn($job);
            }
        }
    }
}
