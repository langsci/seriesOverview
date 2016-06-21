{**
 * plugins/generic/seriesOverview/seriesOverview.tpl
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *
 *}

{include file="frontend/components/header.tpl"}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/seriesOverview/css/seriesOverview.css" type="text/css" />

<script type="text/javascript">
	// Initialize JS handler for catalog header.

	$(function() {ldelim}
		$(".seriesOverviewAccordion" ).accordion({ldelim}autoHeight:false, collapsible: true,active: true,heightStyle:"content" {rdelim});
	{rdelim});

   //capture the click on the a tag
	$(function() {ldelim}
		$(".seriesOverviewAccordion h3 a.linkToSeries").click(function() {ldelim}
      		window.location = $(this).attr('href');
      		return false;
		{rdelim});
	{rdelim});

</script>

{if $useImages}
<style>
  span.ui-icon {ldelim} display:none !important; {rdelim}
	div.header {ldelim} padding:0 !important; {rdelim}
</style>
{/if}

<div id="seriesOverview">

<h2>Series</h2>

{if $series|@count gt 0} 

	{foreach from=$series item=seriesGroup key=key}

		{if $key=="incubation"}
			<p class="sectionHeader">
				{translate key="plugins.generic.seriesOverview.incubationSection"}
			</p>
		{/if}	

		<div class='seriesOverviewAccordion'>
			{foreach from=$seriesGroup item=singleSeries}
				<h3>
					{assign var=seriesId value=$singleSeries.seriesObject->getId()}
					{assign var=monographId value=$mostRecentMonographs[$seriesId]}
					<div class="header">

						{if $useImages && not $monographId}
							<img  class="listIconImage" alt='' src="{url router=$smarty.const.ROUTE_PAGE page="catalog"
						op="thumbnail" type="series" id=$seriesId}">
						{elseif $useImages}
							<img class="listIconImage" alt='' src="{url router=$smarty.const.ROUTE_COMPONENT component="submission.CoverHandler" op="thumbnail" submissionId=$monographId}" />
						{/if}

						<div class="headerText">		
							<span class="seriesTitle">{$singleSeries.seriesObject->getLocalizedFullTitle()}</span>
							<span class='numberOfBooks'">({$singleSeries.numberOfPublishedBooks} {if $singleSeries.numberOfPublishedBooks==1}{translate key="plugins.generic.seriesOverview.book"}{else}{translate key="plugins.generic.seriesOverview.books"}{/if}{if $singleSeries.numberOfForthcomingBooks>0}, {$singleSeries.numberOfForthcomingBooks} {translate key="plugins.generic.seriesOverview.forthcoming"}{/if})
							</span>			
						</div>

						<a href="{url router=$smarty.const.ROUTE_PAGE page="catalog" op="series" path=$singleSeries.link|escape}" class='linkToSeries'>
							{translate key="plugins.generic.seriesOverview.linkToSeries"}
						</a>

					</div>
				</h3> 
				<div class='accordionContentWrapper'>
					<div {if $useImages}class='accordionContentWithImage'{/if}>

					{if $useImages && not $monographId}
						<img class="seriesImage" alt='' src='{url router=$smarty.const.ROUTE_PAGE page="catalog" op="fullSize" type="series" id=$seriesId}'>
					{elseif $useImages}
						<img alt='' src="{url router=$smarty.const.ROUTE_COMPONENT component="submission.CoverHandler" op="cover" submissionId=$monographId}" />
					{/if}

					<div class="bookList">
						<ul>
							{if $singleSeries.numberOfBooks>0}
	    						{foreach from=$singleSeries.monographs item=publishedMonograph}
									<li class='books'>
										<a href="{url router=$smarty.const.ROUTE_PAGE page="catalog" op="book" path=$publishedMonograph.link|escape}">
											{$publishedMonograph.presentationString}
										</a>
									</li>
		    					{/foreach} 
							{else}
								<li>{translate key="plugins.generic.seriesOverview.noPublications"}</li>
							{/if}
						</ul>	
					</div>
					</div>

				</div>

	 		{/foreach} 
		</div>
	 	{/foreach} 

{/if}
</div> 

{include file="frontend/components/footer.tpl"}
