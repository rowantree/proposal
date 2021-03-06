<?php
	/*
	 *
	 * $Id: proposalConfig.php 100 2017-03-02 12:51:49Z stephen $
	 *
	 */

	// Set the default event code

$config = SetConfig();

function SetConfig()
{
	$config = new stdClass();
	$defaultEventCode = 'ROS';

	$config->eventCode = ISSET($_REQUEST['eventCode']) ? strtoupper($_REQUEST['eventCode']) :  $defaultEventCode;

	switch( $config->eventCode )
	{
		case 'FOL':
			$config->event = 'Feast of Lights';
			$config->image = 'http://earthspirit.rowantree.org/images/fol09header.jpg';
			$config->eventDate = 'Date: 10-12 February 2017';
			$config->title = 'Feast of Lights Presenter/Performer Application';
			$config->year = '2017';
			$config->deadLine = 'December 21, 2016';
			$config->decisionDate = "December 31th";
			$config->fb_image = 'http://earthspirit.rowantree.org/images/fol_fb.jpg';
			break;

		case 'ROS':
			$config->event = 'Rites of Spring';
			$config->image = 'http://earthspirit.rowantree.org/images/earlybird39_progproposal.png';
			$config->eventDate = '25 May, 2017';
			$config->title = 'Rites of Spring Presenter/Performer Application';
			$config->year = '2017';
			$config->deadLine = 'March 21, 2017';
			$config->decisionDate = "April 31th";
			$config->fb_image = 'http://earthspirit.rowantree.org/images/ros38_Program_head_fb.gif';
			break;

		default:
			echo "Invalid Event Code $config->eventCode";
			exit;
	}


	$config->proposalTextSize = 500;
	$config->proposalCnt = 4;


	$config->emailNotifyList = "meadwizard@gmail.com, chris@lafond.us, scarthen@gmail.com, darthen@yahoo.com";
	$config->emailNotifyList = "meadwizard@gmail.com, chris@lafond.us, darthen@yahoo.com";
	$config->emailNotifyList = "meadwizard@gmail.com, darthen@yahoo.com, sarah.twichell@gmail.com";

    //$config->emailNotifyList = "meadwizard@gmail.com";

	/*
	 * Note: the field list array elements are
	 *   fieldName
	 *   fieldLabel (descriptive field shown to user)
	 *   fieldType (textbox)
	*/

	if ( $config->eventCode == 'ROS' )
	{
		$config->enableFields = Array(
			'Arrival',
			'NbrOfTimes'
		);
	}
	else {
		$config->enableFields = Array(
			'Available'
		);
	}

	$config->rosFields = Array();

	$config->requiredFields = Array(
		Array('LegalName', 'Legal Name', 'textbox'),
		Array('ProgramName', 'Program Name', 'textbox'),
		Array('email', 'Email Address', 'textbox'),
		Array('phone', 'Telephone Number', 'textbox'),
		Array('unavailable', 'Unavailable Times', 'textbox'),
		Array('biography', 'Biography', 'textbox'),
		//Array('Arrival','When arriving', 'radiobutton'),
		//Array('NumberOfRites','Attended Rites of Spring', 'radiobutton')
	);

	if ( $config->eventCode == 'ROS' )
	{
		$config->rosFields = Array(
			Array('Arrival','When arriving', 'radiobutton'),
			Array('NumberOfRites','Attended Rites of Spring', 'radiobutton')
		);
		//array_push( $requiredFields, $rosFields );
		$config->requiredFields = array_merge($config->requiredFields, $config->rosFields);
	}

	$config->fieldList = array_merge($config->requiredFields);

	$config->optionalFields = Array ();
	for( $idx=1; $idx<=3; ++$idx )
	{
		$propFields = Array(
			Array("LegalName$idx", "Legal Name $idx", "textbox"),
			Array("ProgramName$idx", "Program Name $idx", "textbox"),
			Array("bio$idx", "Bio $idx", "textbox")
		);
		array_push( $config->optionalFields, $propFields );
		$config->fieldList = array_merge($config->fieldList,$propFields);
	}


	// This is a list of the fields that will appear on the proposal section
	$config->proposalFields = Array(
		Array("Title", "Title ", "textbox"),
		Array("PresentationType", "Presentation Type ", "radiobutton"),
		Array("PresentationTypeOther", "Presentation Type Other ", "other"),
		//Array("TargetAudience", "Target Audience ", "radiobutton"),
		Array("Age", "Age ", "radiobutton"),
		Array("AgeOther", "Age Other ", "other"),
		Array("TimePreference", "Time Preference ", "radiobutton"),
		Array("TimePreferenceOther", "Time Preference Other ", "other"),
		Array("SpacePreference", "Space Preference ", "radiobutton"),
		Array("SpacePreferenceOther", "Space Preference Other ", "other"),
		Array("Limit", "Limit ", "radiobutton"),
		Array("LimitOther", "Limit Detail ", "other"),
		Array("Fee", "Fee ", "radiobutton"),
		Array("FeeOther", "Fee Detail ", "other"),
		Array("Presentation", "Presentation ", "textbox"),
		Array("Equipment", "Equipment ", "textbox"),
	);



	$config->proposalList = Array();

	for( $idx=1; $idx<=$config->proposalCnt; ++$idx )
	{
		$propFields = Array();
		foreach( $config->proposalFields as $propfld )
		{
			array_push( $propFields, Array(
				"$propfld[0]$idx",
				"$propfld[1] $idx",
				"$propfld[2]"
				));
		}

			Array(
				Array("Title${idx}", "Title ${idx}", "textbox"),
				Array("PresentationType${idx}", "Presentation Type ${idx}", "radiobutton"),
				Array("PresentationType${idx}Other", "Presentation Type Other ${idx}", "other"),
				//Array("TargetAudience${idx}", "Target Audience ${idx}", "radiobutton"),
				Array("Age${idx}", "Age ${idx}", "radiobutton"),
				Array("Age${idx}Other", "Age Other ${idx}", "other"),
				Array("TimePreference${idx}", "Time Preference ${idx}", "radiobutton"),
				Array("TimePreference${idx}Other", "Time Preference Other ${idx}", "other"),
				Array("SpacePreference${idx}", "Space Preference ${idx}", "radiobutton"),
				Array("SpacePreference${idx}Other", "Space Preference Other ${idx}", "other"),
				Array("Limit${idx}", "Limit ${idx}", "radiobutton"),
				Array("Limit${idx}Other", "Limit Detail ${idx}", "other"),
				Array("Fee${idx}", "Fee ${idx}", "radiobutton"),
				Array("Fee${idx}Other", "Fee Detail ${idx}", "other"),
				Array("Presentation${idx}", "Presentation ${idx}", "textbox"),
			);

		array_push( $config->proposalList, $propFields);
		$config->fieldList = array_merge($config->fieldList,$propFields);
	}


	$config->PresentationType = array (
		array("Workshop", "Workshop"),
		array("Discussion", "Discussion Workshop"),
		array("Ritual", "Ritual"),
		array("Affinity", "Affinity Group"),
		array("Intensive", "Multi-session Intensive Workshop"),
		array("Children", "Children's Workshop"),
		array("NextGen", "Next Gen Workshop (ages 15-25ish)"),
		array("LateNites", "Late Nites Performance"),
		array("Performance", "Other Performance"),
		array("Other", "Other (please explain): ")
		);

	$config->AgeGroup = array (
			array('Everone','Everone'),
			array('18+','18+ only'),
			array('14+','14+'),
			array('8+','8+'),
			array('Children','Children Under 8'),
			array('Family','Families'),
			array('Other','Other (please explain)')
		);


	$config->Audience = array (
			array('Women','Women Only'),
			array('Men','Men Only'),
			array('All','All')
		);

	$config->TimePreference = array (
			array('1.5', '1.5 hours'),
			array('3', '3 hours'),
			array('Other', 'Other (please explain): ')
		);

	$config->AttendeeLimit = array (
		array('No', 'No'),
		array('Other', 'Yes')
	);

	$config->MaterialsFee = array (
		array('No', 'No'),
		array('Other', 'Yes')
	);


	$config->SpacePreference = array (
			array('In','Must be inside'),
			array('Out','Must be outside'),
			array('PreferIn','Prefer inside'),
			array('PreferOut','Prefer outside'),
			array('NoPref','No preference'),
			array('Other','Other (please explain):')
		);

    return $config;
}
?>
