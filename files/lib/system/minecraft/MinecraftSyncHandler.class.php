<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;
use wcf\system\exception\SystemException;
use wcf\util\JSON;

class MinecraftSyncHandler extends MinecraftLinkerHandler implements IMinecraftSyncHandler
{

    /** @var array */
    protected $groups = [];

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        if (!empty($this->groups)) {
            return $this->groups;
        }

        // TODO
        
        return $this->groups;
    }

    /**
     * @inheritDoc
     */
    public function getPlayerGroups(string $uuid)
    {
        $playerGroups = [];
        
        // TODO

        return $playerGroups;
    }

    /**
     * @inheritDoc
     */
    public function addPlayerToGroup(string $uuid, string $group)
    {
        // TODO
        return [];
    }

    /**
     * @inheritDoc
     */
    public function removePlayerFromGroup(string $uuid, string $group)
    {
        // TODO
        return [];
    }

    /**
     * @inheritDoc
     */
    public function sync($minecraftUUID, $groups)
    {
        // Get minecraft Groups on server.
        $args = [
            'type' => 'groupList',
            'content' => [
                'uuid' => $minecraftUUID
            ]
        ];
        $jsonString = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        $rawHasGroups = null;
        try {
            $rawHasGroups = $this->minecraft->getConnection()->call("wsclinker " . $jsonString);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if ($rawHasGroups == null) {
            return ['error' => true, 'message' => 'rawHasGroups is null.'];
        }
        $hasGroups = [];
        try {
            if (!empty($rawHasGroups['S1'])) {
                $hasGroups .= JSON::decode($rawHasGroups['S1']);
            }
            if (!empty($rawHasGroups['S2'])) {
                $hasGroups .= JSON::decode($rawHasGroups['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => 'Could not decode response'];
        }
        // TODO List all groups to sync
        // TODO List all groups from user
        // TODO Get differents
        // TODO Remove Groups
        // TODO Add Groups
    }
}
