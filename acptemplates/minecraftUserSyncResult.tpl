<p>ObjectID: {$objectID}</p>

<p>Hinzugefügt:</p>
{if $added|count}
	<ul>
		{foreach from=$added key=minecraftID item=group}
			<li>
				<p>{$minecraftID}:</p>
				<ul>
					{foreach from=$group key=groupName item=result}
						<li>{$groupName}: {$result['status']}</li>
					{/foreach}
				</ul>
			</li>
		{/foreach}
	</ul>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<p>Entfernt:</p>
{if $removed|count}
	<ul>
		{foreach from=$removed key=minecraftID item=group}
			<li>
				<p>{$minecraftID}:</p>
				<ul>
					{foreach from=$group key=groupName item=result}
						<li>{$groupName}: {$result['status']}</li>
					{/foreach}
				</ul>
			</li>
		{/foreach}
	</ul>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{event name='minecraftUserSync'}