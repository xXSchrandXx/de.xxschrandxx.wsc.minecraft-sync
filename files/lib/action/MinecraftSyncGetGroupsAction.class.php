<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\user\group\minecraft\MinecraftGroupList;
use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;
use wcf\util\JSON;
use wcf\util\MinecraftLinkerUtil;

/**
 * MinecraftSyncGetGroup action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class MinecraftSyncGetGroupsAction extends AbstractMinecraftLinkerAction
{
    /**
     * @var \wcf\data\user\User
     */
    protected $user;

    /**
     * @inheritDoc
     */
    public function readParameters(): ?JsonResponse
    {
        $result = parent::readParameters();

        $this->user = MinecraftLinkerUtil::getUser($this->uuid);
        if (!isset($this->user)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' is not linked.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }

        return $result;
    }
    /**
     * @inheritdoc
     */
    public function execute(): ?JsonResponse
    {
        parent::execute();

        $groupIDs = $this->user->getGroupIDs(true);

        $minecraftGroupList = new MinecraftGroupList();
        $minecraftGroupList->getConditionBuilder()->add('minecraftID = ? AND groupID IN (?)', [$this->minecraftID, $groupIDs]);
        $minecraftGroupList->readObjects();
        /** @var \wcf\data\user\group\minecraft\MinecraftGroup[] */
        $minecraftGroups = $minecraftGroupList->getObjects();

        $minecraftGroupsGroupIDs = [];
        foreach ($minecraftGroups as $minecraftGroup) {
            array_push($minecraftGroupsGroupIDs, $minecraftGroup->getGroupID());
        }

        $userGroupList = new UserGroupList();
        $userGroupList->setObjectIDs($minecraftGroupsGroupIDs);
        $userGroupList->readObjects();
        /** @var \wcf\data\user\group\UserGroup[] */
        $userGroups = $userGroupList->getObjects();

        $shouldHave = [];
        $shouldNotHave = [];
        foreach ($minecraftGroups as $minecraftGroup) {
            if ($minecraftGroup->getShouldHave()) {
                if (array_key_exists($minecraftGroup->getGroupName(), $shouldNotHave)) {
                    if ($userGroups[$minecraftGroup->getGroupID()]->priority > $shouldNotHave[$minecraftGroup->getGroupName()]) {
                        unset($shouldNotHave[$minecraftGroup->getGroupName()]);
                    } else {
                        continue;
                    }
                }
                $shouldHave[$minecraftGroup->getGroupName()] = $userGroups[$minecraftGroup->getGroupID()]->priority;
            } else {
                if (array_key_exists($minecraftGroup->getGroupName(), $shouldHave)) {
                    if ($userGroups[$minecraftGroup->getGroupID()]->priority > $shouldHave[$minecraftGroup->getGroupName()]) {
                        unset($shouldHave[$minecraftGroup->getGroupName()]);
                    } else {
                        continue;
                    }
                }
                $shouldNotHave[$minecraftGroup->getGroupName()] = $userGroups[$minecraftGroup->getGroupID()]->priority;
            }
        }

        ksort($shouldHave, SORT_NUMERIC);
        ksort($shouldNotHave, SORT_NUMERIC);

        return $this->send('OK', 200, [
            'shouldHave' => $shouldHave,
            'shoudlNotHave' => $shouldNotHave
        ]);
    }
}
