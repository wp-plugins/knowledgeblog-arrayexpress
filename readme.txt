=== Knowledgeblog ArrayExpress ===

Contributors: d_swan, philliplord, sjcockell, knowledgeblog
Tags: arrayepress, microarray, transcriptomics, metadata, res-comms, scholar, academic, science
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.1

This plugin extracts metadata from ArrayExpress submission metadata using the [aexp] shortcode.

== Description ==

Interprets the &#91;aexp&#93; shortcode to extract and display metadata from the [ArrayExpress](http://www.ebi.ac.uk/arrayexpress/) XML API.
The correct shortcode format is &#91;aexp = "id"&#93;metadata&#91;/aexp&#93;
Where id is the accession number of the ArrayExpress holding you wish to interrogate and metadata is one of the following types

species			: returns species
accession		: returns linked accession
submissiondate		: returns submission date
lastupdate		: returns last update date
releasedate		: returns release date
name			: returns short experiment description
description		: returns long experiment description
score			: returns MIAME scores (out of 5)
contact			: returns contact email address and role if supplied
citation		: returns citation, publication information and link to PubMed if supplied
datafiles		: returns links to raw and processed data files, sdrf and idf files
png			: returns and links the experimental design png
svg			: returns and links the experimental design svg
assays			: returns the number of assays
samples			: returns the number of samples 
arraydesign		: returns and links to the array platform
experimenttypes		: returns the experiment types (time course data, aCGH, dye swapped etc.)
experimentalfactors	: returns the experiment factors (drug dose, timepoints, genotypes, compounds etc.)
sampleattributes	: returns the sample attributes (organism part, developmental stage, tissue source, sex, strain etc.)

== Installation ==

1. Unzip the downloaded .zip archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use the [aexp][/aexp] shortcode somewhere on your site, to automatically insert ArrayExpress metadata

== Copyright ==

This plugin is copyright Daniel Swan, Newcastle University and is licensed under GPLv2. 
