<?php

namespace wcf\system\minecraft;

use wcf\data\user\User;
use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftList;
use wcf\system\exception\MinecraftException;
use wcf\util\JSON;

class MinecraftSyncSingleHandler implements IMinecraftSyncSingleHandler
{

    /** @var \wcf\data\minecraft\Minecraft */
    protected Minecraft $minecraft;

    /**
     * @inheritDoc
     */
    public function __construct(int $minecraftID)
    {
        $this->minecraft = new Minecraft($minecraftID);
    }

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
        $args = ['type' => 'groupList'];
        $jsonString = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        $result = null;
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonString);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $this->groups;
        }
        if (empty($result)) {
            return $this->groups;
        }
        if ($result['Response'] != 0) {
            return $this->groups;
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response .= JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response .= JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $this->groups;
        }
        if (empty($response)) {
            return $this->groups;
        }
        if ($response['error']) {
            return $this->groups;
        }
        $this->groups = $response['message'];
        return $this->groups;
    }

    /**
     * @inheritDoc
     */
    public function getPlayerGroups(string $uuid)
    {
        $playerGroups = [];
        $args = [
            'type' => 'getPlayerGroups',
            'content' => [
                'uuid' => $uuid
            ]
        ];
        $jsonString = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        $result = null;
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonString);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $playerGroups;
        }
        if (empty($result)) {
            return $playerGroups;
        }
        if ($result['Response'] != 0) {
            return $playerGroups;
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response .= JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response .= JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $playerGroups;
        }
        if (empty($response)) {
            return $playerGroups;
        }
        if ($response['error']) {
            return $playerGroups;
        }
        $playerGroups = $response['message'];
        return $playerGroups;
    }

    /**
     * @inheritDoc
     */
    public function addPlayerToGroup(string $uuid, string $group)
    {
        $args = [
            'type' => 'addPlayerToGroup',
            'content' => [
                'uuid' => $uuid,
                'group' => $group
            ]
        ];
        $jsonString = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        $result = null;
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonString);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if (empty($result)) {
            return ['error' => true, 'message' => 'Result is empty'];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands'];
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response .= JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response .= JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => 'Could not decode response'];
        }
        if (empty($response)) {
            return ['error' => true, 'message' => 'Response is empty'];
        }
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function removePlayerFromGroup(string $uuid, string $group)
    {
        $args = [
            'type' => 'removePlayerFromGroup',
            'content' => [
                'uuid' => $uuid,
                'group' => $group
            ]
        ];
        $jsonString = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        $result = null;
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonString);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if (empty($result)) {
            return ['error' => true, 'message' => 'Result is empty'];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands'];
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response .= JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response .= JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => 'Could not decode response'];
        }
        if (empty($response)) {
            return ['error' => true, 'message' => 'Response is empty'];
        }
        return $response;
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
                'uuid' => $uuid
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
