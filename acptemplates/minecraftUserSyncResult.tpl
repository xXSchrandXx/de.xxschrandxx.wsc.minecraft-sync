<dl>
	<dt>{lang}wcf.page.groupAddSection.minecraftSync.minecraftUserSyncResult.objectID{/lang}</dt>
	<dd>{#$objectID}</dd>
</dl>
{if $error|count}
<div class="error">
	{foreach from=$error key=minecraftID item=errors}
		<dl>
			<dt>{$minecraftTitel[$minecraftID]} ({$minecraftID})</dt>
			<dd>
				<ol>
					{foreach from=$errors item=info}
						<li>
							<dl>
								<dt>{lang}wcf.page.groupAddSection.minecraftSync.minecraftUserSyncResult.error.statusCode{/lang}</dt>
								<dd>{#$info['statusCode']}</dd>
							</dl>
							<dl>
								<dt>{lang}wcf.page.groupAddSection.minecraftSync.minecraftUserSyncResult.error.status{/lang}</dt>
								<dd>{$info['status']}</dd>
							</dl>
						</li>
					{/foreach}
				</ol>
			</dd>
		</dl>
	{/foreach}
</div>
{/if}
<dl>
	<dt>{lang}wcf.page.groupAddSection.minecraftSync.minecraftUserSyncResult.added{/lang}</dt>
	<dd>
	{if $added|count}
		<ul>
			{foreach from=$added key=minecraftID item=group}
				<li>
					<dl>
						<dt>{$minecraftTitel[$minecraftID]} ({$minecraftID})</dt>
						<dd>
							<ul>
								{foreach from=$group key=groupName item=result}
									<li>{$groupName}: {$result['status']}</li>
								{/foreach}
							</ul>
						</dd>
					</dl>
				</li>
			{/foreach}
		</ul>
	{else}
		<p class="info">{lang}wcf.global.noItems{/lang}</p>
	{/if}
	</dd>
</dl>
<dl>
	<dt>{lang}wcf.page.groupAddSection.minecraftSync.minecraftUserSyncResult.removed{/lang}</dt>
	<dd>
	{if $removed|count}
		<ul>
			{foreach from=$removed key=minecraftID item=group}
				<li>
					<dl>
						<dt>{$minecraftTitel[$minecraftID]} ({$minecraftID})</dt>
						<dd>
							<ul>
								{foreach from=$group key=groupName item=result}
									<li>{$groupName}: {$result['status']}</li>
								{/foreach}
							</ul>
						</dd>
					</dl>
				</li>
			{/foreach}
		</ul>
	{else}
		<p class="info">{lang}wcf.global.noItems{/lang}</p>
	{/if}
	</dd>
</dl>

{event name='minecraftUserSync'}

{if $benchmark|isset}
	<hr />
	<dl>
		<dt>ExecutionTime</dt>
		<dd>{#$benchmark['ExecutionTime']}s</dd>
	</dl>
	<dl>
		<dt>MemoryUsage</dt>
		<dd>{#$benchmark['MemoryUsage']} MB</dd>
	</dl>
{/if}