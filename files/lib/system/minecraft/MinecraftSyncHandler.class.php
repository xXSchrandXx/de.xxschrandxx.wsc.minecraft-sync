<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use wcf\data\user\group\UserGroupList;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\User;
use wcf\system\exception\SystemException;
use wcf\util\JSON;
use wcf\util\StringUtil;

class MinecraftSyncHandler extends AbstractMultipleMinecraftHandler implements IMinecraftSyncHandler
{
    /**
     * Baut die Klasse auf
     */
    public function init()
    {
        if (MINECRAFT_SYNC_IDENTITY) {
            $this->minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_SYNC_IDENTITY));
        }
        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function status(?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $stati = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $stati[$minecraftID] = $this->status($minecraftID);
            }
            return $stati;
        } else {
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call($minecraftID, 'GET', 'permission/status');
                return JSON::decode($response->getBody());
            } catch (GuzzleException | SystemException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return false;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function groupList(?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $groups = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $groups[$minecraftID] = $this->groupList($minecraftID);
            }
            return $groups;
        } else {
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call($minecraftID, 'GET', 'permission/groupList');
                $responseBody = JSON::decode($response->getBody());
                return $responseBody['groups'];
            } catch (GuzzleException | SystemException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return false;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getUserGroups(string $uuid, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $playerGroups = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $playerGroups[$minecraftID] = $this->getUserGroups($uuid, $minecraftID);
            }
            return $playerGroups;
        } else {
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call($minecraftID, 'POST', 'permission/getUserGroups', [
                    'uuid' => $uuid
                ]);
                $responseBody = JSON::decode($response->getBody());
                return $responseBody['groups'];
            } catch (GuzzleException | SystemException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return false;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function addUserToGroup(string $uuid, string $group, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $responses = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $response[$minecraftID] = $this->addUserToGroup($uuid, $group, $minecraftID);
            }
            return $responses;
        } else {
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call($minecraftID, 'POST', 'permission/addUserToGroup', [
                    'uuid' => $uuid,
                    'group' => $group
                ]);
                return JSON::decode($response->getBody());
            } catch (GuzzleException | SystemException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return [
                    'status' => $e->getMessage(),
                    'statusCode' => $e->getCode()
                ];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function removeUserFromGroup(string $uuid, string $group, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $responses = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $responses[$minecraftID] = $this->removeUserFromGroup($uuid, $group, $minecraftID);
            }
            return $responses;
        } else {
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call($minecraftID, 'POST', 'permission/removeUserFromGroup', [
                    'uuid' => $uuid,
                    'group' => $group
                ]);
                return JSON::decode($response->getBody());
            } catch (GuzzleException | SystemException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return [
                    'status' => $e->getMessage(),
                    'statusCode' => $e->getCode()
                ];
            }
        }
    }

    private $wscGroups = [];

    /**
     * @inheritDoc
     */
    public function getWSCGroups()
    {
        if (!empty($this->wscGroups)) {
            return $this->wscGroups;
        }

        $wscGroupList = new UserGroupList();
        $wscGroupList->getConditionBuilder()->add('minecraftGroups IS NOT NULL');
        $wscGroupList->readObjects();

        foreach ($wscGroupList as $userGroup) {
            try {
                $this->wscGroups[$userGroup->groupID] = JSON::decode($userGroup->minecraftGroups);
            } catch (SystemException $e) {
            }
        }
        wcfDebug($this->wscGroups);

        return $this->wscGroups;
    }

    /**
     * @inheritDoc
     */
    public function sync(MinecraftUser $minecraftUser)
    {
        // 1. UUID & User
        $uuid = $minecraftUser->minecraftUUID;
        $user = new User($minecraftUser->userID);

        // 2. Benutzergruppen vom WSC erhalten
        $wscGroups = $this->getWSCGroups();

        // 3. Liste alle Gruppen des Benutzers auf
        $userGroupIDs = $user->getGroupIDs();

        // 4. Auflisten welche Gruppen der Benutzer haben sollte
        $shouldHave = [];
        foreach ($wscGroups as $wscGroup) {
            if (in_array($wscGroup->groupID, $userGroupIDs)) {
                $shouldHave[$wscGroup->groupID] = $wscGroup->minecraftGroups;
            }
        }

        // 5. Auflisten welche Gruppen der Benutzer nicht haben sollte
        $shouldNotHave = [];
        foreach ($wscGroups as $wscGroup) {
            if (!in_array($wscGroup->groupID, $userGroupIDs)) {
                $shouldNotHave[$wscGroup->groupID] = $wscGroup->minecraftGroups;
            }
        }

        // 6. Benutzergruppen von Minecraft-Servern erhalten
        $minecraftGroups = $this->getUserGroups($uuid);

        // 7. Gruppen müssen hinzugefügt werden
        $needToAdd = [];
        foreach ($minecraftGroups as $minecraftID => $hasGroups) {
            foreach ($hasGroups as $hasGroup) {
                if (!in_array($hasGroup, $shouldHave)) {
                    \array_push($needToAdd[$minecraftID], $hasGroup);
                }
            }
        }

        // 8. Gruppen müssen entfernt werden
        $needToRemove = [];
        foreach ($minecraftGroups as $minecraftID => $hasGroups) {
            foreach ($hasGroups as $hasGroup) {
                if (in_array($hasGroup, $shouldNotHave)) {
                    \array_push($needToRemove[$minecraftID], $hasGroup);
                }
            }
        }

        wcfDebug($needToAdd, $needToRemove);

        // TODO Add Groups
        foreach ($needToAdd as $minecraftID => $groups) {
            foreach ($groups as $group) {
                $this->addUserToGroup($minecraftID, $uuid, $group);
            }
        }

        // TODO Remove Groups
        foreach ($needToRemove as $minecraftID => $groups) {
            foreach ($groups as $group) {
                $this->removeUserFromGroup($minecraftID, $uuid, $group);
            }
        }
    }
}
