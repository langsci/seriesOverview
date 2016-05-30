<?php

/**
 * @file plugins/generic/seriesOverview/LangsciCommonFunctions.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 */

	// get 'Publication date' for publication format 'PDF'
	function getPublicationDate($submissionId) {

		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		if ($publishedMonograph) {

			$pubformats = $publishedMonograph->getPublicationFormats();
			for ($i=0; $i<sizeof($pubformats); $i++) {

				$formatName = implode($pubformats[$i]->getName());
				if ($formatName=="PDF") {

					$pubdates = $pubformats[$i]->getPublicationDates();
					$pubdatesArray = $pubdates->toArray();
					for ($ii=0;$ii<sizeof($pubdatesArray);$ii++) {
						// role id "01": publication date
						if ($pubdatesArray[$ii]->getRole()=="01") {
							return $pubdatesArray[$ii]->getDate();
						}
					}
				}
			}
		}
		return null;
	}

	function getSubmissionPresentationString($submissionId) {

		$langsciCommonDAO = new LangsciCommonDAO;

		// get monograph and authors object
		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		$monographObject = $publishedMonograph;
		if (!$publishedMonograph) {
			$monographDAO = new MonographDAO;
 			$monograph = $monographDAO -> getById($submissionId);
			if (!$monograph) {
				return "Invalid  monograph id: " . $submissionId;
			}
			$monographObject = $monograph;
		}
		$authors = $monographObject->getAuthors();

		$contextId = $monographObject->getContextId();

		// is edited volume
		$editedVolume = false;
		if ($monographObject->getWorkType()== WORK_TYPE_EDITED_VOLUME) {
			$editedVolume = true;
		}

		// get authors to be printed (all volume editors for edited volumes, all authors else)
		$numberOfAuthors = 0;
		$authorsInBiblio = array();

		for ($i=0; $i<sizeof($authors); $i++) {
			$userGroupId = $authors[$i]->getUserGroupId();

			if ($editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Volume Editor",$contextId)) {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			} else if (!$editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Author",$contextId))  {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			}
		}

		// get author string
		$authorString=""; 
		for ($i=0; $i<sizeof($authorsInBiblio); $i++) {
			if ($i>0) {
				$authorString = $authorString . ", ";
			}
			$authorString = $authorString .
				$authorsInBiblio[$i]->getFirstName() . " " .  $authorsInBiblio[$i]->getLastName();
		}
			
		if ($authorString=="") {
			$authorString = "N.N.";
		}

		if ($editedVolume) {
			$authorString = "edited by " . $authorString;
		}
		$title = $monographObject->getLocalizedFullTitle(); 

		return $title ." <span class='editor'>(" . $authorString . ")</span>" ;
		return $authorString . ": " . $title ."." ;
	}

	function getBiblioLinguistStyle($submissionId) {

		$langsciCommonDAO = new LangsciCommonDAO;

		// get monograph and authors object
		$publishedMonographDAO = new PublishedMonographDAO;
 		$publishedMonograph = $publishedMonographDAO->getById($submissionId);
		$monographObject = $publishedMonograph;
		if (!$publishedMonograph) {
			$monographDAO = new MonographDAO;
 			$monograph = $monographDAO -> getById($submissionId);
			if (!$monograph) {
				return "Invalid  monograph id: " . $submissionId;
			}
			$monographObject = $monograph;
		}
		$authors = $monographObject->getAuthors();

		$contextId = $monographObject->getContextId();

		// get series information				
		$seriesId = $monographObject->getSeriesId();
		$seriesDAO = new SeriesDAO;
		$series = $seriesDAO -> getById($seriesId,1);
		if (!$series) {
			$seriesTitle = "Series unknown";
			$seriesPosition="tba";
		} else {
			$seriesTitle = $series->getLocalizedFullTitle();
			$seriesPosition = $monographObject ->getSeriesPosition();
			if (empty($seriesPosition)) {
				$seriesPosition="tba";
			}
		}

		// is edited volume
		$editedVolume = false;
		if ($monographObject->getWorkType()== WORK_TYPE_EDITED_VOLUME) {
			$editedVolume = true;
		}

		// get authors to be printed (all volume editors for edited volumes, all authors else)
		$numberOfAuthors = 0;
		$authorsInBiblio = array();
		for ($i=0; $i<sizeof($authors); $i++) {
			$userGroupId = $authors[$i]->getUserGroupId();
			if ($editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Volume Editor",$contextId)) {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			} else if (!$editedVolume && $userGroupId==$langsciCommonDAO->getUserGroupIdByName("Author",$contextId))  {
				$numberOfAuthors = $numberOfAuthors + 1;
				$authorsInBiblio[] = $authors[$i];
			}
		}

		// get author string
		$authorString=""; 
		for ($i=0; $i<sizeof($authorsInBiblio); $i++) {

			// format for first author: last_name, first_name, for all others: first_name last_name
			if ($i==0) {
				$authorString = $authorString .
					$authorsInBiblio[$i]->getLastName() . ", " .  $authorsInBiblio[$i]->getFirstName();
			} else {	
				// separator between authors
				if ($i==$numberOfAuthors-1) {
					$authorString = $authorString . " & ";
				} else {
					$authorString = $authorString . ", ";												
				}
				$authorString = $authorString .
					$authorsInBiblio[$i]->getFirstName() . " " . $authorsInBiblio[$i]->getLastName();
			}
		}

		// get author string: for edited volumes: add (ed.)/(eds.)	
		if ($editedVolume && $numberOfAuthors==1) {
			$authorString = $authorString . " (ed.)";
		} else if ($editedVolume && $numberOfAuthors>1) {
			$authorString = $authorString . " (eds.)";
		}
		$authorString = $authorString . ". ";

		// get author string: if there are no authors: add N.N.		
		if ($authorString==". ") {
			$authorString = "N.N. ";
		}
	
		// get year of publication, only for published mongraphs
		$publicationDateString = getPublicationDate($submissionId);
		if (!$publicationDateString) {
			$publicationDateString = "????";
		} else {
			$publicationDateString = substr($publicationDateString,0,4); 
		}

		// get title
		$title = $monographObject->getLocalizedFullTitle($submissionId);
		if (!$title) {
			$title = "Title unknown";
		}				

		// compose biblio string
		$biblioLinguisticStyle = $authorString . $publicationDateString .
				".<i> " . $title . "</i> (".$seriesTitle  . " " . $seriesPosition ."). Berlin: Language Science Press.";
		
		return $biblioLinguisticStyle;
	}	

?>
