{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.minecraftSync.canManage') && MINECRAFT_SYNC_ENABLED && MINECRAFT_SYNC_IDENTITY}

	<a href="#" title="{lang}wcf.page.minecraftSyncUserAdd.button.status{/lang}" class="minecraftSyncButton jsToolTips">
		<span class="icon icon24 fa-wifi"></span>
	</a>

	<script data-relocate="true">
		require(["xXSchrandXx/Minecraft/MinecraftSync"], function(MinecraftSync) {
			new MinecraftSync.default();
		});
	</script>
{/if}
<p>aa</p>