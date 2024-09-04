<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use wcf\data\user\group\minecraft\MinecraftGroupList;
use wcf\data\user\group\UserGroupList;
use wcf\system\endpoint\GetRequest;
use wcf\system\exception\UserInputException;
use wcf\util\MinecraftLinkerUtil;

#[GetRequest('/xxschrandxx/minecraft/{id:\d+}/{uuid:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}}/groups')]
final class GetGroups extends AbstractMinecraftLinker
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_SYNC_ENABLED'];

    /**
     * @inheritDoc
     */
    public $availableMinecraftIDs = MINECRAFT_SYNC_IDENTITY;

    /**
     * @inheritDoc
     */
    public function execute(): ResponseInterface
    {
        $user = MinecraftLinkerUtil::getUser($this->uuid);
        if (!isset($user)) {
            if (ENABLE_DEBUG_MODE) {
                throw new UserInputException('Bad Request. \'uuid\' is not linked.', 400);
            } else {
                throw new UserInputException('Bad request.', 400);
            }
        }

        $groupIDs = $user->getGroupIDs(true);

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

        return new JsonResponse([
            'shouldHave' => $shouldHave,
            'shouldNotHave' => $shouldNotHave
        ]);
    }
}
