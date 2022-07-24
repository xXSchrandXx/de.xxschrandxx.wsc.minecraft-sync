<?php

namespace wcf\data\user\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\minecraft\MinecraftList;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

class MinecraftUserSyncAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = MinecraftUserEditor::class;

    /**
     * list of permissions required sync objects
     * @var string[]
     */
    protected $permissionsSync = ['admin.minecraftSync.canManage'];

    public function validateSync()
    {
        // validate permissions
        if (\is_array($this->permissionsSync) && !empty($this->permissionsSync)) {
            WCF::getSession()->checkPermissions($this->permissionsSync);
        } else {
            throw new PermissionDeniedException();
        }

        if (empty($this->getObjects())) {
            $this->readObjects();
        }

        if (empty($this->getObjects())) {
            throw new UserInputException('objectIDs');
        }
    }

    public function sync()
    {
        $handler = MinecraftSyncHandler::getInstance();
        $responses = [];
        foreach ($this->getObjects() as $editor) {
            $responses[$editor->getObjectID()] = $handler->sync($editor->getDecoratedObject());
        }
        $minecraftTitel = [];
        $minecrafts = $handler->getMinecrafts();
        foreach ($minecrafts as $minecraft) {
            $minecraftTitel[$minecraft->getObjectID()] = $minecraft->name;
        }
        $templates = [];
        foreach ($responses as $objectID => $response) {
            $variables = $response;
            $variables['minecraftTitel'] = $minecraftTitel;
            $variables['objectID'] = $objectID;
            $tmpTemplate = WCF::getTPL()->fetch('minecraftUserSyncResult', 'wcf', $variables);
            $template = \str_replace(["\\", "\n", "\t", "\r"], '', StringUtil::unifyNewlines($tmpTemplate));
            $templates[$objectID] = $template;
        }
        return $templates;
    }
}
