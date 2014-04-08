{if isset($rows)}
{foreach from=$rows item=row}
<!-- item -->
<article class="item" data-href="#" data-log="#" data-gameid="{$row.gameId}">
	<div class="licon dpkey"><strong class="key">{$row.number}</strong> </div>
	<div class="mcon">
		<div class="appicon"> <img src="{$row.iconUrl}"/> </div>
		<div class="rbtn"> 
			<!--下载按钮--> 
			<a href="#"><button class="go_app" data-target="getsms">短信发送</button></a> 
		</div>
		<div class="info">
			<div class="title"> 
				<!--标题--> 
				<a>{$row.title}</a> 
			</div>
		</div>
	</div>
</article>                                
{/foreach}
{/if}