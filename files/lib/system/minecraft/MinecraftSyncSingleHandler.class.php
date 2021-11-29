<?php

namespace wcf\system\minecraft;

use wcf\data\user\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftList;
use wcf\system\exception\MinecraftException;
use wcf\util\JSON;

abstract class MinecraftSyncSingleHandler extends SpigotMinecraftLinkerHandler
{
    /** @var array */
    protected $groups = [];

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
                $response = $response + JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response = $response + JSON::decode($result['S2']);
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
}
