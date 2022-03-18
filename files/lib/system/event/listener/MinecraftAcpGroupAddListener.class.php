<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserGroupEditForm;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\exception\UserInputException;
use wcf\system\exception\SystemException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\minecraft\MinecraftSyncMultipleHandler;
use wcf\system\WCF;
use wcf\util\JSON;

class MinecraftAcpGroupAddListener implements IParameterizedEventListener
{
    /**
     * Liste der Minecraft Server Gruppen
     *
     * @var array
     */
    protected $minecraftGroupNames = [];

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(MINECRAFT_SYNC_ENABLED && MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftSynchronisation.canManage')) {
            return;
        }

        $this->$eventName($eventObj);
    }

    /**
     * @see AbstractForm::readFormparameters()
     */
    public function readFormParameters()
    {
        if (isset($_POST['minecraftGroupNames'])) {
            $this->minecraftGroupNames = $_POST['minecraftGroupNames'];
        }
    }

    /**
     * @see AbstractForm::validate()
     */
    public function validate($eventObj)
    {
        foreach (MinecraftSyncHandler::getInstance()->getMinecrafts() as $minecraft) {
            // TODO
        }
    }

    /**
     * @see AbstractForm::save()
     */
    public function save($eventObj)
    {
        $eventObj->additionalFields = array_merge($eventObj->additionalFields, [
            'minecraftGroups' => JSON::encode($this->minecraftGroups)
        ]);

        /* TODO
        if (MINECRAFT_SYNC_ENABLE_BACKGROUND_JOB) {
            BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncBackgroundJob());
        }
        */

        // // reset values
        if (!($eventObj instanceof UserGroupEditForm)) {
            $this->minecraftGroups = [];
        }
    }

    /**
     * @see AbstractForm::assignVariables()
     */
    public function assignVariables($eventObj)
    {
        if (empty($_POST) && $eventObj instanceof UserGroupEditForm) {
            try {
                $this->minecraftGroups = JSON::decode($eventObj->group->minecraftGroups);
            } catch (SystemException $e) {
                // do nothing
            }
        }

        $minecraft = MinecraftSyncHandler::getInstance();

        // assign variables
        WCF::getTPL()->assign([
            'minecrafts' => $minecraft->getMinecrafts(),
            'minecraftGroupNames' => $minecraft->getGroups()
        ]);
    }
}
