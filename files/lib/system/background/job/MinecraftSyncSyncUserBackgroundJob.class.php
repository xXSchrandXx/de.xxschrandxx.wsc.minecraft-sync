<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\exception\MinecraftException;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncSyncUserBackgroundJob extends AbstractBackgroundJob
{
    protected ?int $userID;
    protected array $unsetGroups;

    public function __construct(int $userID = null, array $unsetGroups = [])
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
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        $responses = MinecraftSyncHandler::getInstance()->syncUser($this->userID, $this->unsetGroups);
        foreach ($responses as $minecraftID => $response) {
            if ($response['statusCode'] < 200 && $response['statusCode'] >= 300) {
                if (array_key_exists('retryAfter', $response)) {
                    $this->retryAfter = $response['retryAfter'];
                }
                throw new MinecraftException('statusCode is not between 199 and 300', $response['statusCode']);
            }
        }
    }
}
