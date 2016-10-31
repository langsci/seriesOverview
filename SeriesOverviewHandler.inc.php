<?php

/**
 * @file plugins/generic/seriesOverview/SeriesOverviewHandler.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesOverviewHandler
 *
 *
 */

import('classes.handler.Handler');
import('plugins.generic.seriesOverview.LangsciCommonDAO');
import('classes.monograph.PublishedMonographDAO');
import('classes.monograph.MonographDAO');
import('classes.press.SeriesDAO');
include('LangsciCommonFunctions.inc.php');

class SeriesOverviewHandler extends Handler {	

	static $plugin;

	function SeriesOverviewHandler() {
		parent::Handler();
	}

	/**
	 * Provide the plugin to the handler.
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	function viewSeriesOverview($args, $request) {

		$langsciCommonDAO = new LangsciCommonDAO;

		$press = $request->getPress();
		$seriesDao = DAORegistry::getDAO('SeriesDAO');
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');

		$seriesIterator = $seriesDao->getByPressId($press->getId());
		$series = array();
		$monographs = array();
		$mostRecentMonograph = array();

		while ($seriesObject = $seriesIterator->next()) {

			$seriesId=$seriesObject->getId();
			$publishedMonographs = $publishedMonographDao->getBySeriesId($seriesId, $press->getId())->toAssociativeArray();
			$numberOfBooks = sizeof($publishedMonographs);	
						
			$seriesGroup = 'incubation';
			if ($numberOfBooks>0) {
				$seriesGroup = 'series';
			}
			$series[$seriesGroup][$seriesId]['seriesObject'] = $seriesObject;
			$series[$seriesGroup][$seriesId]['title'] = $seriesObject->getLocalizedTitle();
			$series[$seriesGroup][$seriesId]['numberOfBooks'] = $numberOfBooks;		
			$series[$seriesGroup][$seriesId]['link'] = $seriesObject->getPath();
					
			$monographsForSeries = array();
			$numberOfPublishedBooks = 0;

			foreach ($publishedMonographs as $monographKey => $publishedMonograph) {

				$localizedFullTitle = $publishedMonograph->getLocalizedFullTitle();

				if (strpos($localizedFullTitle,'Forthcoming:')===false) {
					$numberOfPublishedBooks++;
				}
				$monographsForSeries[$monographKey]['submissionId'] = $monographKey;
				$monographsForSeries[$monographKey]['publicationDate'] = getPublicationDate($monographKey);
				$monographsForSeries[$monographKey]['fullTitle'] = $localizedFullTitle;
				$monographsForSeries[$monographKey]['title'] = $publishedMonograph->getLocalizedTitle();
				$monographsForSeries[$monographKey]['presentationString'] = getSubmissionPresentationString($monographKey);
				$monographsForSeries[$monographKey]['link'] = $monographKey;
			}
			usort($monographsForSeries,'sort_books_by_title');
			$series[$seriesGroup][$seriesId]['monographs'] = $monographsForSeries;
			$series[$seriesGroup][$seriesId]['numberOfPublishedBooks'] = $numberOfPublishedBooks;
			$series[$seriesGroup][$seriesId]['numberOfForthcomingBooks'] = $numberOfBooks-$numberOfPublishedBooks;	
		}
		
		krsort($series);
		
		if (array_key_exists('incubation',$series) && sizeof($series['incubation'])) {
			usort($series['incubation'],'sort_by_title_and_numberOfBooks');
		}
		if (array_key_exists('series',$series) && sizeof($series['series'])) {
			usort($series['series'],'sort_by_title_and_numberOfBooks');
		}

		$mostRecentMonographs = array(); // key: seriesId, value: id of most recent monograph
		if (array_key_exists('series',$series)) {
			foreach ($series['series'] as $singleSeries) { 
				$publicationDates = array();
				$seriesId = $singleSeries['seriesObject']->getId();

				foreach ($singleSeries['monographs'] as $key=>$monograph) {
					$publicationDates[$monograph['publicationDate']] = $monograph['submissionId'];
				}	
				krsort($publicationDates);
				$dates = array_keys($publicationDates);
				if ($dates[0]) {
					$mostRecentMonographs[$seriesId] = $publicationDates[$dates[0]];
				} else {
					$mostRecentMonographs[$seriesId] = null;
				}
			}
		}	

		$templateMgr = TemplateManager::getManager($request);
		$this->setupTemplate($request); // important for getting the correct menue
		$templateMgr->assign('mostRecentMonographs', $mostRecentMonographs);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.seriesOverview');
		$templateMgr->assign('baseUrl',$request->getBaseUrl());	
		$templateMgr->assign('monographs',$monographs);
		$templateMgr->assign('series',$series);
		$templateMgr->assign('useImages',self::$plugin->getSetting($press->getId(), 'useImages'));	

		$seriesOverviewPlugin = PluginRegistry::getPlugin('generic', SERIESOVERVIEW_PLUGIN_NAME);
		$templateMgr->display($seriesOverviewPlugin->getTemplatePath().'seriesOverview.tpl');
	}
}

function sort_by_title_and_numberOfBooks($a, $b) {
	if ($b['numberOfBooks']!=$a['numberOfBooks']) {
		return  $b['numberOfBooks'] - $a['numberOfBooks'];
	} else {
    	return strcasecmp($a['title'],$b['title']);
	}
}

function sort_books_by_title($a, $b) {
	$aForthcoming = strpos($a['fullTitle'],'Forthcoming:');
	$bForthcoming = strpos($b['fullTitle'],'Forthcoming:');
	if ($aForthcoming===false) {
		if ($bForthcoming===false) {
			return strcasecmp($a['title'],$b['title']);
		} else {
			return -1;
		}
	}
	else {
		if ($bForthcoming===false) {
			return 1;
		} else {
			return strcasecmp($a['title'],$b['title']);
		}
	}
}


?>
