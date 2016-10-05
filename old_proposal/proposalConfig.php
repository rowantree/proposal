<?php
	/*
	 *
	 * $Id: proposalConfig.php 68 2016-02-28 22:10:19Z stephen $
	 *
	 */

	// Set the default event code
	$defaultEventCode = 'FOL';
	$eventCode = ISSET($_REQUEST['eventCode']) ? strtoupper($_REQUEST['eventCode']) :  $defaultEventCode;

	switch( $eventCode )
	{
		case 'FOL':
			$event = 'Feast of Lights';
			$image = 'http://earthspirit.rowantree.org/images/fol09header.jpg';
			$eventDate = 'Date: 10-12 February 2017';
			$title = 'Feast of Lights Presenter/Performer Application';
			$year = '2017';
			$deadLine = 'December 21, 2016';
			$decisionDate = "December 31th";
			$fb_image = 'http://earthspirit.rowantree.org/images/fol_fb.jpg';
			break;

		case 'ROS':
			$event = 'Rites of Spring';
			$image = 'http://earthspirit.rowantree.org/images/earlybird38_progproposal.png';
			$eventDate = '25 May, 2016';
			$title = 'Rites of Spring Presenter/Performer Application';
			$year = '2016';
			$deadLine = 'March 1, 2016';
			$decisionDate = "April 31th";
			$fb_image = 'http://earthspirit.rowantree.org/images/ros38_Program_head_fb.gif';
			break;

		default:
			echo "Invalid Event Code $eventCode";
			exit;
	}


	$proposalTextSize = 500;
	$proposalCnt = 4;


	$emailNotifyList = "meadwizard@gmail.com";
	$emailNotifyList = "meadwizard@gmail.com, chris@lafond.us, scarthen@gmail.com, darthen@yahoo.com";
	$emailNotifyList = "meadwizard@gmail.com, chris@lafond.us, darthen@yahoo.com";
	$emailNotifyList = "meadwizard@gmail.com, darthen@yahoo.com, sarah.twichell@gmail.com";

	/*
	 * Note: the field list array elements are
	 *   fieldName
	 *   fieldLabel (descriptive field shown to user)
	 *   fieldType (textbox)
	*/

	if ( $eventCode == 'ROS' )
	{
		$enableFields = Array(
			'Arrival',
			'NbrOfTimes'
		);
	}
	else {
		$enableFields = Array();
	}

	$rosFields = Array();

	$requiredFields = Array(
		Array('LegalName', 'Legal Name', 'textbox'),
		Array('ProgramName', 'Program Name', 'textbox'),
		Array('email', 'Email Address', 'textbox'),
		Array('phone', 'Telephone Number', 'textbox'),
		Array('unavailable', 'Unavailable Times', 'textbox'),
		Array('biography', 'Biography', 'textbox'),
		//Array('Arrival','When arriving', 'radiobutton'),
		//Array('NumberOfRites','Attended Rites of Spring', 'radiobutton')
	);

	if ( $eventCode == 'ROS' )
	{
		$rosFields = Array(
			Array('Arrival','When arriving', 'radiobutton'),
			Array('NumberOfRites','Attended Rites of Spring', 'radiobutton')
		);
		//array_push( $requiredFields, $rosFields );
		$requiredFields = array_merge($requiredFields, $rosFields);
	}

	$fieldList = array_merge($requiredFields);

	$optionalFields = Array ();
	for( $idx=1; $idx<=3; ++$idx )
	{
		$propFields = Array( 
			Array("LegalName$idx", "Legal Name $idx", "textbox"),
			Array("ProgramName$idx", "Program Name $idx", "textbox"),
			Array("bio$idx", "Bio $idx", "textbox")
		);
		array_push( $optionalFields, $propFields );
		$fieldList = array_merge($fieldList,$propFields);
	}


	// This is a list of the fields that will appear on the proposal section
	$proposalFields = Array(
		Array("Title", "Title ", "textbox"),
		Array("PresentationType", "Presentation Type ", "radiobutton"),
		Array("PresentationTypeOther", "Presentation Type Other ", "other"),
		Array("TargetAudience", "Target Audience ", "radiobutton"),
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
	);



	$proposalList = Array();

	for( $idx=1; $idx<=$proposalCnt; ++$idx )
	{
		$propFields = Array();
		foreach( $proposalFields as $propfld )
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
				Array("TargetAudience${idx}", "Target Audience ${idx}", "radiobutton"),
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

		array_push( $proposalList, $propFields);
		$fieldList = array_merge($fieldList,$propFields);
	}


	$PresentationType = array (
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

	$AgeGroup = array (
			array('18+','18+ only'),
			array('14+','14+'),
			array('11+','11+'),
			array('Children','Children'),
			array('Family','Whole Family'),
			array('Other','Other (please explain)')
		);


	$Audience = array (
			array('Women','Women Only'),
			array('Men','Men Only'),
			array('All','All')
		);

	$TimePreference = array (
			array('1.5', '1.5 hours'),
			array('3', '3 hours'),
			array('Other', 'Other (please explain): ')
		);

	$AttendeeLimit = array (
		array('No', 'No'),
		array('Other', 'Yes')
	);

	$MaterialsFee = array (
		array('No', 'No'),
		array('Other', 'Yes')
	);


	$SpacePreference = array (
			array('In','Must be inside'),
			array('Out','Must be outside'),
			array('PreferIn','Prefer inside'),
			array('PreferOut','Prefer outside'),
			array('NoPref','No preference'),
			array('Other','Other (please explain):')
		);
?>
