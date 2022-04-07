<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\system\minecraft\MinecraftSyncHandler;

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
        MinecraftSyncHandler::getInstance()->syncAll();
    }
}
