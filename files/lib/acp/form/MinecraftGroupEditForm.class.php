<?php

namespace wcf\acp\form;

use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TitleFormField;

/**
 * MinecraftGroup edit acp form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftGroupEditForm extends MinecraftGroupAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';
}
