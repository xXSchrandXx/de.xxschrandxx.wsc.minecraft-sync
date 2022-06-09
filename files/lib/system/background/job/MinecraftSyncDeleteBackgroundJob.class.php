<?php

namespace wcf\system\background\job;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncDeleteBackgroundJob extends AbstractBackgroundJob
{
    protected MinecraftUser $minecraftUser;

    public function __construct(MinecraftUser $minecraftUser)
    {
        $this->minecraftUser = $minecraftUser;
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
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        $responses = MinecraftSyncHandler::getInstance()->delete($this->minecraftUser);
        // TODO fail on TooManyConnections in responses
        foreach ($responses as $minecraftID => $response) {
            if (array_key_exists('retryAfter', $response)) {
                $this->retryAfter = $response['retryAfter'];
                $this->fail();
            }
        }
    }
}
