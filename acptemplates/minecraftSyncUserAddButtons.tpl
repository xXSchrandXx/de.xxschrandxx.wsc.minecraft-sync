<a href="#" title="{lang}wcf.page.minecraftSyncUserAdd.button.status{/lang}" class="minecraftSyncButton jsToolTips">
	<span class="icon icon16 fa-refresh"></span>
</a>

<script data-relocate="true">
	require(["xXSchrandXx/Minecraft/MinecraftSync", "Language"], function(MinecraftSync, Language) {
		Language.addObject({
			'wcf.page.minecraftSyncUserAdd.button.status.result': '{lang}wcf.page.minecraftSyncUserAdd.button.status.result{/lang}'
		});
		new MinecraftSync.default();
	});
</script>