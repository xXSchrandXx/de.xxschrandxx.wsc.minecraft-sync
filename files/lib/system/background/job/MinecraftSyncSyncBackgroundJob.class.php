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
            $responses = MinecraftSyncHandler::getInstance()->syncLatest();
            // TODO fail on TooManyConnections in responses
            foreach ($responses as $minecraftID => $response) {
                if (array_key_exists('retryAfter', $response)) {
                    $this->retryAfter = $response['retryAfter'];
                    $this->fail();
                }
            }
        } else {
            $responses = MinecraftSyncHandler::getInstance()->syncUser($this->userID, $this->unsetGroups);
            // TODO fail on TooManyConnections in responses
            foreach ($responses as $minecraftID => $response) {
                if (array_key_exists('retryAfter', $response)) {
                    $this->retryAfter = $response['retryAfter'];
                    $this->fail();
                }
            }
        }
    }
}
