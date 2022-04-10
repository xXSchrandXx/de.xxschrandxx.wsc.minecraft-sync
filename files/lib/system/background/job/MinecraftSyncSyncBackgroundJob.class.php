<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
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

    /**
     * @inheritDoc
     */
    public function retryAfter()
    {
        switch ($this->getFailures()) {
            case 1:
                return 5 * 60;
            case 2:
                return 30 * 60;
            case 3:
                return 2 * 60 * 60;
        }
    }

    /**
     * @inheritDoc
     */
    public function perform()
    {
        if (MINECRAFT_SYNC_ENABLED) {
            if ($this->userID === null) {
                $responses = MinecraftSyncHandler::getInstance()->syncAll($this->unsetGroups);
                // TODO fail on TooManyConnections in responses
            } else {
                $responses = MinecraftSyncHandler::getInstance()->syncUser($this->userID, $this->unsetGroups);
                // TODO fail on TooManyConnections in responses
            }
        }
    }
}
