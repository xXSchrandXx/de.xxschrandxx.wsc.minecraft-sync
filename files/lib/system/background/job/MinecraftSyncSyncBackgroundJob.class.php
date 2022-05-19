<?php

namespace wcf\system\background\job;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\minecraft\MinecraftHandler;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncSyncBackgroundJob extends AbstractBackgroundJob
{
    protected ?int $userID;
    protected array $unsetGroups;

    public function __construct(array $unsetGroups = [], ?int $userID = null)
    {
        $this->userID = $userID;
        $this->unsetGroups = $unsetGroups;
    }

    protected $retryAfter = 3 * 10;

    /**
     * @inheritDoc
     */
    public function retryAfter()
    {
        return $this->retryAfter;
    }

    /**
     * @inheritDoc
     */
    public function perform()
    {
        if (MINECRAFT_SYNC_ENABLED) {
            return;
        }
        if ($this->userID === null) {
            MinecraftSyncHandler::getInstance()->syncLatest();
            // TODO fail on TooManyConnections in responses
            // Queue multiple syncMultiple??
            // Waiting until 5.5 update
        } else {
            MinecraftSyncHandler::getInstance()->syncUser($this->userID, $this->unsetGroups);
            // TODO fail on TooManyConnections in responses
            // Waiting until 5.5 update
        }
    }
}
