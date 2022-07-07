<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncSyncUserBackgroundJob;

class MinecraftSyncCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncSyncUserBackgroundJob());
    }
}
