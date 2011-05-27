<?php

/*
  Plugin Name: Knowledgeblog ArrayExpress
  Plugin URI: http://knowledgeblog.org/knowledgeblog-arrayexpress
  Description: This plugin provides a shortcode handler to allow extraction of metadata about an ArrayExpress experiment from the XML endpoint (http://www.ebi.ac.uk/arrayexpress/)
  Version: 0.1
  Author: Daniel Swan
  Author URI: http://knowledgeblog.org
  Email: knowledgeblog-discuss@knowledgeblog.org
  License: GPL2
  Copyright 2011.  Daniel Swan (dswan@bioinformatics.org)
  Newcastle University	
*/

class AExpress {

function init() {
	//Initialise the shortcode [aexp]
	add_shortcode('aexp',array(__CLASS__, 'arrayexpress'));
	}		

function getExperimentSpecies($parsedXML) {
	//Assumes one species per experiment, no counter-examples yet
	return $parsedXML->experiment->species;
	}

function getExperimentAccession($parsedXML, $experimentID) {
	//Should know this already, it's in the shortcode
	return "<a href=\"http://www.ebi.ac.uk/arrayexpress/experiments/".$parsedXML->experiment->accession."\">$experimentID</a>";
	}

//The next 3 functions deal with dates of submission, update and release

function getExperimentSubmissionDate($parsedXML) {
        return $parsedXML->experiment->submissiondate;
        }

function getExperimentUpdateDate($parsedXML) {
        return $parsedXML->experiment->lastupdatedate;
        }

function getExperimentReleaseDate($parsedXML) {
	return $parsedXML->experiment->releasedate;
	}

function getExperimentName($parsedXML) {
	//Name is the short description of an ArrayExpress holding
	return $parsedXML->experiment->name;
	}

function getDescriptionText($parsedXML) {
	//Description is an abstract style summarisation of the holding
	return $parsedXML->experiment->description->text;
	}

function getExperimentMIAMEScore($parsedXML) {
	//There are 5 components to a 'MIAME score' a score out of 5 and can be retrieved directly
	//Array design means the design is also banked with ArrayExpress
	//Experiment factors describe the conditions of the experiment
	//There are scores for the availability of raw and processed data
	//Protcols mean the investigation description is completed
	$arrayDesign = $parsedXML->experiment->miamescores->reportersequencescore;
	$experimentFactors = $parsedXML->experiment->miamescores->factorvaluescore;
	$processedData = $parsedXML->experiment->miamescores->derivedbioassaydatascore;
	$rawData = $parsedXML->experiment->miamescores->measuredbioassaydatascore;
	$protcol = $parsedXML->experiment->miamescores->protocolscore;
	$overallScore = $parsedXML->experiment->miamescores->overallscore;
	return "Total score: $overallScore (Array designs: $arrayDesign; Protocols: $protocol; Factors: $experimentFactors; Processed data: $processedData; Raw data: $rawData)"; 
	}

function getContact($parsedXML) {
	//Not all contacts have a role, return name, email and role if available
	if (empty($parsedXML->experiment->provider->role)) {
		return $parsedXML->experiment->provider->contact." (<a href=\"mailto:".$parsedXML->experiment->provider->email."\">".$parsedXML->experiment->provider->email."</a>)";
		} else {
	return $parsedXML->experiment->provider->contact." (<a href=\"mailto:".$parsedXML->experiment->provider->email."\">".$parsedXML->experiment->provider->email."</a>) [".$parsedXML->experiment->provider->role."]";	
		}
	}

function getCitation($parsedXML) {
	//Do not return citation if a citation does not exist. Return only the title if there is no PubMed ID.  Return everything if there is a PubMed ID
	if (empty($parsedXML->experiment->bibliography)) {
		return;
		}
	elseif (empty($parsedXML->experiment->bibliography->accession)) {
		return $parsedXML->experiment->bibliography->title;		
		} else {
		return $parsedXML->experiment->bibliography->title." ".$parsedXML->experiment->bibliography->authors." <i>".$parsedXML->experiment->bibliography->publication."</i> ".$parsedXML->experiment->bibliography->volume."(".$parsedXML->experiment->bibliography->issue.")".$parsedXML->experiment->bibliography->pages." <a href=\"http://www.ncbi.nlm.nih.gov/pubmed/".$parsedXML->experiment->bibliography->accession."\">PubMed</a>";
		}
	}

function linkToFiles($parsedXML, $baseFilesURL, $experimentID) {
	//Recapitulates links to raw and processed data files, sample/data relationship file, investigation description file
	$rawDataLink = "Raw data files: <a href=\""."$baseFilesURL"."$experimentID"."/".$parsedXML->experiment->files->raw["name"]."\">".$parsedXML->experiment->files->raw["name"]."</a>";
	$processedDataLink = "Processed data files: <a href=\""."$baseFilesURL"."$experimentID"."/".$parsedXML->experiment->files->fgem["name"]."\">".$parsedXML->experiment->files->fgem["name"]."</a>";
	$investigationDescription = "Investigation description: <a href=\""."$baseFilesURL"."$experimentID"."/".$parsedXML->experiment->files->idf["name"]."\">".$parsedXML->experiment->files->idf["name"]."</a>";
	$sampleDataRelations = "Sample/Data relationship: <a href=\""."$baseFilesURL"."$experimentID"."/".$parsedXML->experiment->files->sdrf["name"]."\">".$parsedXML->experiment->files->sdrf["name"]."</a>";
	return $rawDataLink."\n".$processedDataLink."\n".$investigationDescription."\n".$sampleDataRelations;
	}

function linkToPNG($parsedXML, $baseFilesURL, $experimentID) {
	//Returns a scaled PNG file of the experiment design
	return "<a href=\""."$baseFilesURL"."$experimentID/".$parsedXML->experiment->files->biosamples->png["name"]."\"><img src=\""."$baseFilesURL"."$experimentID/".$parsedXML->experiment->files->biosamples->png["name"]."\" width=\"400\"></a>";
	}

function linkToSVG($parsedXML, $baseFilesURL, $experimentID) {
	//Returns a scaled SVG file of the experiment design
        return "<a href=\""."$baseFilesURL"."$experimentID/".$parsedXML->experiment->files->biosamples->svg["name"]."\"><img src=\""."$baseFilesURL"."$experimentID/".$parsedXML->experiment->files->biosamples->svg["name"]."\" width=\"400\"></a>";
        }

function assays($parsedXML) {
	//Number of assays in the experiment
	return $parsedXML->experiment->assays;
	}

function samples($parsedXML) {
	//Number of samples in the experiment
	return $parsedXML->experiment->samples;
	}

function arrayDesign($parsedXML) {
	//Return and link to the array design
	//WARNING: Unsure if there can be experiments with multiple array designs, and this does not handle them if they do
	$arrayID = $parsedXML->experiment->arraydesign->accession;
	$arrayname = $parsedXML->experiment->arraydesign->name;
	return "<a href=\"http://www.ebi.ac.uk/arrayexpress/arrays/$arrayID\">".$arrayID."</a> (".$arrayname.")";
	}

function experimentTypes($parsedXML) {
	//Experiment types include things like whether it is time course data, aCGH, dye swapped etc.
        $stringOfTypes="";
        $experimentTypes = $parsedXML->experiment->experimentdesign;
        foreach ($experimentTypes as $singleType) {
                $stringOfTypes=$singleType.", ".$stringOfTypes;
        }
        return substr($stringOfTypes,0,-2);
}

function experimentalFactors($parsedXML) {
	//Experimental factors are the groupings for the experimental comparisons - drug dose, timepoints, genotypes, compounds etc.
	//WISHLIST: Would be nice to have this rendered as a table from an array rather than appending elements to strings repeatedly
        $experimentalFactors = $parsedXML->xpath('//experimentalfactor');
	$return="";
        while(list( , $node) = each($experimentalFactors)) {
                $stringOfValues="";
                $arrayKey =  $node->name;
                foreach ($node->value as $value) {
                        $stringOfValues=$value.", ".$stringOfValues;
                        }               
                $arrayElement = substr($stringOfValues,0,-2);
		$return .= "<br><b>$arrayKey</b>: $arrayElement";
		}
                return $return;
}

function sampleAttributes($parsedXML) {
	//Sample attributes are additional sample information that may be relevant, organism part, developmental stage, tissue source, sex, strain etc.
        //WISHLIST: Would be nice to have this rendered as a table from an array rather than appending elements to strings repeatedly
	$sampleAttributes = $parsedXML->xpath('//sampleattribute');
        $return="";
        while(list( , $node) = each($sampleAttributes)) {
                $stringOfValues="";
                $arrayKey =  $node->category;
                foreach ($node->value as $value) {
                        $stringOfValues=$value.", ".$stringOfValues;
                        }
                $arrayElement = substr($stringOfValues,0,-2);
                $return .= "<br><b>$arrayKey</b>: $arrayElement";
                }
                return $return;
	}

function arrayexpress($accession, $type) {
	//The shortcode is processed from this format [aexp id="M-EXP-NNNN"]text to replace[/aexp]
	//$accession is the ArrayExpress accession number, the id="M-EXP-NNNN" part of the shortcode
	//$type is the text to replace.  Ideally this would allow XPath queries, but there is not so much metadata to make this worthwhile just yet.
	//Consequently $type has a numnber of mostly human readable shortcuts for the information you want have replaced in the shortcode
	//There is no checking for sanity of the accession number, so use real ones

	$baseQueryURL="http://www.ebi.ac.uk/arrayexpress/xml/v2/experiments/";
	$baseFilesURL="http://www.ebi.ac.uk/arrayexpress/files/";
	$experimentID = $accession["id"];
	$fullQueryURL=$baseQueryURL.$accession["id"];
	
	//Pull the XML document via curl
	$curlhandle = curl_init();
	curl_setopt($curlhandle, CURLOPT_URL,$fullQueryURL);
	curl_setopt($curlhandle, CURLOPT_VERBOSE, 0);
	curl_setopt($curlhandle, CURLOPT_POST, 0);
	curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER,1);
	$xmldocreturn = curl_exec($curlhandle);
	curl_close ($curlhandle);

	$parsedXML = new SimpleXMLElement($xmldocreturn);

	//WISHLIST: In addition to named shortcode replacement text, allow the use of XPath queries directly
	//WISHLIST: Set a shortcode to specify an experiment id so that id=" " can be dropped from subsequent shortcodes and inferred instead

	if ($type == "species") {
		return AExpress::getExperimentSpecies($parsedXML);
		}
	elseif ($type == "accession") {
		return AExpress::getExperimentAccession($parsedXML, $experimentID);
		}
	elseif ($type == "submissiondate") {
		return AExpress::getExperimentSubmissionDate($parsedXML);
		}
	elseif ($type == "lastupdate") {
		return AExpress::getExperimentUpdateDate($parsedXML);
		}
	elseif ($type == "releasedate") {
		return AExpress::getExperimentReleaseDate($parsedXML);
		}
	elseif ($type == "name")	{
		return AExpress::getExperimentName($parsedXML);
		}
	elseif ($type == "description") {
		return AExpress::getDescriptionText($parsedXML);
		}
	elseif ($type == "score") {
		return AExpress::getExperimentMIAMEScore($parsedXML);
		}
	elseif ($type == "contact") {
		return AExpress::getContact($parsedXML);
		}
	elseif ($type == "citation") {
		return AExpress::getCitation($parsedXML);
		}
	elseif ($type == "datafiles") {
		return AExpress::linkToFiles($parsedXML, $baseFilesURL, $experimentID);
		}
	elseif ($type == "png") {
		return AExpress::linkToPNG($parsedXML, $baseFilesURL, $experimentID);
		}
	elseif ($type == "svg") {
                return AExpress::linkToSVG($parsedXML, $baseFilesURL, $experimentID);
		}
	elseif ($type == "assays") {
		return AExpress::assays($parsedXML);
		}
	elseif ($type == "samples") {
		return AExpress::samples($parsedXML);
		}
	elseif ($type == "arraydesign") {
		return AExpress::arrayDesign($parsedXML);
		}
	elseif ($type == "experimenttypes") {
		return AExpress::experimentTypes($parsedXML);
		}
	elseif ($type == "experimentalfactors") {
		return AExpress::experimentalFactors($parsedXML);
		}
	elseif ($type == "sampleattributes") {
		return AExpress::sampleAttributes($parsedXML);
		}
	else {
	//return what is between the shortcode
	return $type;
		}
	}
}

AExpress::init();

//That's all Folks!
?>
