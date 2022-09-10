{include file='header' pageTitle='wcf.acp.form.minecraftGroupAdd.formTitle.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.form.minecraftGroupAdd.formTitle.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
				<a href="{link controller='UserGroupEdit' id=$groupID}#minecraft-sync-{$minecraftID}{/link}" class="button">
                	{lang}wcf.global.button.back{/lang}
				</a>
			</li>
            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}