<dl class="{'minecraftGroupNames-'|concat:$minecraftID}{if $errorField == 'minecraftGroupNames-'|concat:$minecraftID} formError{/if}">
	<dt>
		<label for="{'minecraftGroupNames-'|concat:$minecraftID}">
			{lang}wcf.page.groupAddSection.minecraftSync.minecraftGroupNames{/lang}
		</label>
		<a href="#" title="{lang}wcf.global.button.refresh{/lang}" class="minecraftGroupListButton jsToolTip">
			<span class="icon icon16 fa-refresh"></span>
		</a>
	</dt>
	<dd>
		<ul class="scrollableCheckboxList" id="{'minecraftGroupNames-'|concat:$minecraftID}" style="height: 200px;">
			{foreach from=$minecraftGroupNames[$minecraftID] item=minecraftGroupName}
				<li>
					<label>
						<input type="checkbox" name="minecraftGroupNames[{$minecraftID}][]" value="{@$minecraftGroupName}"
							{if !$minecraftGroups[$minecraftID]|empty && $minecraftGroupName|in_array($minecraftGroups[$minecraftID])}
								checked
							{/if}
						>
						{$minecraftGroupName}
					</label>
				</li>
			{/foreach}
		</ul>
		{if $errorField == 'minecraftGroupNames-'|concat:$minecraftID}
			<small>{lang}wcf.page.groupAddSection.minecraftSync.error.{$errorType}{/lang}</small>
		{/if}
		<small>{lang}wcf.page.groupAddSection.minecraftSync.minecraftGroupName.description{/lang}</small>
	</dd>
</dl>

<script data-relocate="true">
	require(["xXSchrandXx/Minecraft/MinecraftGroupList", "Language"], function(MinecraftGroupList, Language) {
		Language.addObject({
			'wcf.global.success': '{lang}wcf.global.success{/lang}'
		});
		new MinecraftGroupList.default();
	});
	require(['Language', 'WoltLabSuite/Core/Ui/ItemList/Filter'], function(Language, UiItemListFilter) {
		Language.addObject({
			'wcf.global.filter.button.visibility': '{jslang}wcf.global.filter.button.visibility{/jslang}',
			'wcf.global.filter.button.clear': '{jslang}wcf.global.filter.button.clear{/jslang}',
			'wcf.global.filter.error.noMatches': '{jslang}wcf.global.filter.error.noMatches{/jslang}',
			'wcf.global.filter.placeholder': '{jslang}wcf.global.filter.placeholder{/jslang}',
			'wcf.global.filter.visibility.activeOnly': '{jslang}wcf.global.filter.visibility.activeOnly{/jslang}',
			'wcf.global.filter.visibility.highlightActive': '{jslang}wcf.global.filter.visibility.highlightActive{/jslang}',
			'wcf.global.filter.visibility.showAll': '{jslang}wcf.global.filter.visibility.showAll{/jslang}'
		});

		new UiItemListFilter('minecraftGroupNames-{$minecraftID|encodeJS}');
	});
</script>