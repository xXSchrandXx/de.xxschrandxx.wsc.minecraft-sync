<dl>
	<dt>
		<label for="minecraftGroupNames-{$minecraftID}">
			{lang}wcf.page.groupAddSection.minecraftSync.minecraftGroupNames{/lang}
		</label>
	</dt>
	<dd>
		<ul class="scrollableCheckboxList" id="minecraftGroupNames-{$minecraftID}" style="height: 200px;">
			{foreach from=$minecraftGroupNames[$minecraftID] item=minecraftGroupName}
				<li>
					<label><input type="checkbox" name="minecraftGroupNames[{$minecraftID}][]" value="{@$minecraftGroupName}"{if !$minecraftGroups[$minecraftID]|empty && $minecraftGroupName|in_array($minecraftGroups[$minecraftID])} checked{/if}> {$minecraftGroupName}</label>
				</li>
			{/foreach}
		</ul>
		<small>{lang}wcf.page.groupAddSection.minecraftSync.minecraftGroupName.description{/lang}</small>
	</dd>
</dl>

<script data-relocate="true">
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
