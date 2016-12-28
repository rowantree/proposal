<?php
	session_start();
	Include "proposalConfig.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Presentation Proposals for <?php echo $config->event;?></title>
<meta content="text/html; charset=ISO-8859-1" http-equiv="Content-Type">
<meta property="og:title" content="Presentation Proposals for <?php echo $config->event;?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="http://earthspirit.rowantree.org/proposal/proposal.php" />
<meta property="og:image" content="<?php echo $config->fb_image;?>" />
<link rel="stylesheet" href="proposal.css" type="text/css">
<?php
    //<script src='https://www.google.com/recaptcha/api.js'></script>
	//require('../recaptcha-master/src/autoload.php');
	//require_once('recaptchalib.php');
	//$publickey = '6LePYgsAAAAAABRVY4sGhED8v-HR_jyBsUtlf_J-';

	$data = array();
	if (IsSet($_SESSION['RegData'])) { $data = $_SESSION['RegData']; }


	/*
	echo "<!--\n";
	foreach( $data as $key => $value ) 
	{
		echo "$key => '$value'\n";
	}
	echo "-->\n";
	*/

/*
 * ToDo:
 * Make "other" fields only available when they click other.
 * Add field requirements - must answer 'most' fields
 * Remote Coundown fields from result file.
 *
 * $Id: proposal.php 90 2016-02-28 23:09:38Z stephen $
 */
?>
	

<script type="text/javascript" src="proposal.js"></script>

<script language="javascript" type="text/javascript">

	var proposalCnt = <?php echo $config->proposalCnt;?>;


	/*
		*
		* S T A R T
		*
	*/

	function start()
	{
		// Hide the user need javascript message
		var msg = document.getElementById("JSREQUIRED");
		if (msg) msg.style.display='none';


		// hide all the proposal sections
		/*
		for ($idx=1; $idx<=proposalCnt; ++$idx)
		{
			ShowHideProp($idx);
		}

		for ($idx=1; $idx<=3; ++$idx)
		{
			ShowHideBio($idx);
		}
		*/


		// hide all the "other" input fields
		var fields = document.getElementsByTagName("input");
		for (var i in fields)
		{
			//if ( !confirm(fields[i].className) ) return;
			if ( fields[i].className == "other" )
			{
				fields[i].style.visible = 'hidden';
			}
		}

		// uncomment to test submission
		//alert('Hi');
		//document.forms[0]["Arrival"][2].checked = true;
		//SubmitForm();

		CheckFields();

		// Open preloaded presentor or proposal sections
<?php
	for ($idx=1; $idx <= $config->proposalCnt ; ++ $idx)
	{
		if (IsSet($data["Title$idx"]) && $data["Title$idx"] != "")
		{
			echo "ShowUseProp($idx);\n";
		}
	}

	for ($idx=1; $idx <= 3 ; ++ $idx)
	{
		if (IsSet($data["LegalName$idx"]) && $data["LegalName$idx"] != '')
		{
			echo "ShowUseBio($idx);\n";
		}
	}
?>



	}


<?php
	echo "<!-- Dump requiredFields as fieldList -->\n";
	echo "var fieldList = Array(\n";
	$char='';
	foreach( $config->requiredFields as $fieldInfo )
	{
		echo "\t${char}{ name:'$fieldInfo[0]', desc:'$fieldInfo[1]', type:'$fieldInfo[2]' }\n";
		$char = ',';
	}
	echo ");\n";

	echo "var proposalFields = Array(\n";
	$char='';
	foreach( $config->proposalFields as $propfld )
	{
		echo "\t${char}{ name:'$propfld[0]', desc:'$propfld[1]', type:'$propfld[2]' }\n";
		$char = ',';
	}
	echo ");\n";


	echo "var proposalList = Array(\n";
	$char2='';
	foreach( $config->proposalList as $proposalFields )
	{
		echo "\t${char2}Array(\n";
		$char='';
		foreach( $proposalFields as $fieldInfo )
		{
			echo "\t\t${char}{ name:'$fieldInfo[0]', desc:'$fieldInfo[1]', type:'$fieldInfo[2]' }\n";
			$char = ',';
		}
		echo "\t)\n";
		$char2=',';
	}
	echo ");\n";



?>


</script>

	<title><?php echo $title;?></title>

</head>

<body link="#0000FF" vlink="#FF0000" alink="#000088" onload="start()" >
<div class="image"><img src="<?php echo $config->image;?>"/></div>
<div id="JSREQUIRED">
This page requires that JavaScript be active!
</div>
<div id="IEWARN">
Warning: This page does not currently work correctly with Internet Explorer. <br>
If you are using that browser you'll need<br>
to try a different one until we resolve the issue.<br>
</div>
<h1><?php echo "$config->eventDate";?><br>
<?php echo $config->year;?>&nbsp;
PRESENTER &amp; PERFORMER APPLICATION PAGE<br>
</h1>
<br>

<?php
	$postError = ISSET($_REQUEST['POSTERROR']) ? $_REQUEST['POSTERROR'] : '';
	if ($postError == 'RECAPTCHA')
	{
		echo "<h2 style=\"color:red\">Please complete the ReCaptcha before submitting the page</h2>";
	}
?>

<form method="post" action="proposalSubmit.php">
<input type="hidden" name="eventCode" value="<?php echo $config->eventCode;?>"/>

<?php if ($config->eventCode=='FOL') { ?>
<p class="c9">
A Feast of Lights will feature programs with several important themes this year:
    <ul>
<li>Culture/traditions, healing, divination, makers/crafts, arts/music, spiritual practice and activism/service.</li>
<li>In our selection process, we will prioritize offerings on these topics for adults, children and families.</li>
<li>Panels or performances with 3-4 members are welcome and can be included using this same form.</li>
    </ul>

If you are interested in presenting a workshop or performance,
leading a discussion or a ritual or hosting an activity for children,
please complete the form below and return it to us by <?php echo $config->deadLine?>.
The first round of program decisions will be made at that time.
Proposals received after that date may be chosen if any slots remain available.
Please order your proposals in order of your preference.
Since this is only a weekend event, we are most likely to select only one of your proposals and will not accept more than two.
Thank you.

</p>
<?php } else if ($config->eventCode=='ROS') { ?>

<p class="c9">
The Rites of Spring program is centered around pagan and earth-centered culture, community and spiritual practices.
We prioritize offerings on these themes: rituals from different traditions, connecting with our natural physical environment, specific spiritual practices, musical and artistic endeavors, hands-on creative activities and the practical applications of pagan values in the world - through service, lifestyle choices or activism.
</p>
<p class="c9">
If you are interested in presenting a workshop or performance, leading a discussion or a ritual or hosting a children’s activity, please fill out the form below and return it to us by <?php echo $config->deadLine?>.  The first round of program decisions will be made by April 1.  Proposals received after the deadline may still be selected if any slots remain available.  All decisions will be made by May 1.  Please list your proposals in order of your preference. We are most likely to select only one of your proposals and will not accept more than two unless the third is a program specifically for children or families. Thank you.
</p>

<?php } else {?>
<p class="c9">If you are interested in presenting a workshop or performance, leading a
discussion or affinity group, leading a ritual or children&#8217;s
activity, please fill out the form below and return it to us by <span class="c8"><?php echo $config->deadLine;?></span>.
Program decisions will be made at that time. Please order your
proposals in order of your preference; we cannot guarantee more than
one presentation slot per person.</p>

<p class="c9">Please include a maximum of three proposals. There is space for a 
fourth below only for those who are propsing multi-session intensives.</p>

<?php } 

//function GetDataString( $lbl, $escape = true ) 
function TextField($name, $size=50, $maxLength=50, $onblur='na', $class='na')
{
	global $data;
	echo "<input ";
	if ($class != 'na') echo "class=\"$class\" ";
	if ($onblur != 'na') echo "onBlur=\"$onblur\" ";
	echo " size=\"$size\" maxlength=\"$maxLength\" type=\"text\" name=\"$name\" id=\"$name\"";
	echo " value=\"" , isset($data[$name]) ? $data[$name] : '', "\">";
}



?>







<h2>Be sure to click on the SUBMIT button at the bottom of this page when you are finished.</h2>

<table>
<tr>
	<th>Legal Name</th>
	<td><?php TextField('LegalName')?></td>
</tr>
<tr>
	<th>Name as you would like it<br>to appear in the Program:</th>
	<td><?php TextField('ProgramName')?></td>
</tr>
<tr>
	<th>Email address</th>
	<td><?php TextField('email')?></td>
</tr>
<tr>
	<th>Contact telephone number</th>
	<td><?php TextField('phone')?></td>
</tr>
</table>

<?php 
	/* 
	 * Arrival 
	 */
	if (in_array('Arrival',$config->enableFields)) {
?>
<div class="q">When are you arriving at Rites this year?
<div class="choice">
<label><input name="Arrival" value="VB" type="radio">Village Builders' Assembly</label><br>
<label><input name="Arrival" value="WedPM" type="radio">Wed before 6pm</label><br>
<label><input name="Arrival" value="WedNite" type="radio">Wed after 6pm</label><br>
<label><input name="Arrival" value="Fri" type="radio">Friday eve</label><br>
</div></div>
<br>
<?php
    }
    if (in_array('Available',$config->enableFields)) {
?>
    <div class="q">When are you available at Feast of Lights this year?
        <div class="choice">
            <label><input name="AvailFri3" type="checkbox">Friday (after 3pm)</label><br>
            <label><input name="AvailFri8" type="checkbox">Friday (after 8pm)</label><br>
            <label><input name="AvailSat"  type="checkbox">Saturday</label><br>
            <label><input name="AvailSun"  type="checkbox">Sunday (until 3pm)</label><br>
        </div>
        Comment:
        <?php TextField('available',100,100);?>
    </div>
    <br>

<?php
    }
?>


<div class="q">Please list any days and times during the event when you are UNABLE to present.
<div class="choice">
	<i>(If you have no time restrictions, please write "none.")</i>
	<br>
	<?php TextField('unavailable',100,100);?>
</div></div>

<?php 
	/* 
	 * NbrOfTime 
	 */
	if (in_array('NbrOfTimes',$config->enableFields)) {
?>
<div class="q">How many times have you attended Rites of Spring?
<div class="choice">
<label><input name="NumberOfRites" value="1-3" type="radio">1-3</label><br>
<label><input name="NumberOfRites" value="4-6" type="radio">4-6</label><br>
<label><input name="NumberOfRites" value="7-10" type="radio">7-10</label><br>
<label><input name="NumberOfRites" value="10+" type="radio">10+</label><br>
</div></div>
<?php } ?>

<h3>Presenter or Performer Biography:&nbsp; (You may copy and paste into
 this box, the limit is still 500 characters or about 100 words.)</h3>
<span class="c8">Please include ONE biography that will serve for all of your proposed presentations. </span><br><br>

If you wish us to include special formatting in the program, use the following:<br>
For italics, put an underscore before and after the words to be italicized: _italic words_<br>
For boldface put a star before and after the words to be italicized: *boldface words*<br>

Please do not use all capital letters.<br>

<?php 
function TextAreaWithCounter($name,$onblur='')
{
	global $data;
	echo '<textarea rows="12" cols="80" style="width: 800px; height: 200px;"';
	echo " name=\"$name\" id=\"$name\" ";
	if ($onblur != '') echo " onBlur=\"$onblur\" ";
	echo " onkeydown=\"limitText(this.form.$name,this.form.cnt$name,500);\"";
	echo " onkeyup=\"limitText(this.form.$name,this.form.cnt$name,500);\">";
	if (isset($data[$name])) echo $data[$name];
	echo "</textarea><br>";
	echo "<font size=\"1\">(Maximum characters: 500)<br>";
	echo "You have <input readonly=\"readonly\" name=\"cnt$name\" size=\"3\" value=\"500\" type=\"text\"> characters left.</font>";
}
	TextAreaWithCounter('biography');
/*
<textarea 
	rows="12" cols="80"
	style="width: 800px; height: 200px;" 
	name="biography" 
	id="biography" 
	onkeydown="limitText(this.form.biography,this.form.countdown,500);" 
	onkeyup="limitText(this.form.biography,this.form.countdown,500);"></textarea><br>
<font size="1">(Maximum characters: 500)<br>
You have <input readonly="readonly" id="countdown" name="countdown" size="3" value="500" type="text"> characters left.</font>
 */
?>
<hr>
<span class="c8">Please include one bio for each additional presenter</span>
<?php
/*
 * Additional Bio Fields
 */

for ($idx=1; $idx <= 3 ; ++ $idx)
{
?>
<br><h2 style="display:inline">Additional Presenter (#<?php echo ($idx+1);?>)</h2>

<button type="button" id="UseBioBtn<?php echo $idx;?>" onClick="ShowUseBio(<?php echo $idx;?>);return false;">Use</button>

<div id="BioCtrl<?php echo $idx;?>" style="display:none;">



<button type="button" id="BioButton<?php echo $idx;?>" onClick="ShowHideBio(<?php echo $idx;?>);return false;">Hide</button>
<div id="BioMsg<?php echo $idx;?>" style="display:inline">unused</div>
<!--
<input type="checkbox" 
	name="BioRadioButton<?php echo $idx;?>"
	value="bio"
	onClick="ToggleBio(<?php echo $idx;?>)"
	id="BioRadioButton<?php echo $idx;?>"
>
-->

<div class="prop" id="Bio<?php echo $idx;?>">


<div class="q">Legal Name
<div class="choice">
<?php TextField("LegalName$idx", 100, 100, "CheckFields()");?>
</div></div>

<div class="q">Name as you would like it<br>to appear in the Program:
<div class="choice">

<?php TextField("ProgramName$idx",100,100,'CheckFields()');?>
</div></div>
<?php

	TextAreaWithCounter("bio$idx", 'CheckFields()');
/*
<textarea 
	rows="12" cols="80"
	style="width: 800px; height: 200px;" 
	name="bio<?php echo $idx;?>" 
	id="bio<?php echo $idx;?>" 
	onBlur="CheckFields()"
	onkeydown="limitText(this.form.bio<?php echo $idx;?>,this.form.countdown,500);" 
	onkeyup="limitText(this.form.bio<?php echo $idx;?>,this.form.countdown,500);"></textarea><br>
<font size="1">(Maximum characters: 500)<br>
You have <input readonly="readonly" name="countdown<?php echo $idx;?>" size="3" value="500" type="text"> characters left.</font>
 */
?>
</div>

</div> <!-- Bio Control -->
<?php
}
?>


<hr style="height:6px; background:green">

<h2>NOTE:</h2>

<ul>

  <li class="c6">Check the radio button below to display the fields for each proposal</li>

  <li class="c6">Please fill in <span class="c8">ALL</span> information for each proposal. Leaving out any information may result in your proposal not being accepted. </li>
  
  <li class="c6">Please edit your proposals to <?php echo $config->proposalTextSize;?> characters or less (approximately 100 words).</li>
  
<?php if ($config->eventCode=='ROS') { ?>

  <li class="c6">If your proposal is for a multi-session <span class="c8">intensive</span>, 
	please title each session using the <span class="c8">same name</span>, 
	with a subtitle added (or "part 1," "part 2," etc.). For example, either:

  <ul>
    <li class="c6">"The Basics of Magic Intensive, Part 1," "The Basics of Magic Intensive, Part 2," etc., OR</li>
    <li class="c6">"The Basics of Magic Intensive 1: Grounding," "The Basics of Magic Intensive 2: Candles," etc.</li>
  </ul>
<?php } ?>

</li>

</ul>

<?php

function RadioListOption($label, $fieldName, $dataArray, $fldIdx)
{
	echo '<div class="q">';
	echo $label;
	echo '<div class="choice">';
	RadioList($fieldName, $dataArray, $fldIdx);
	echo '</div></div>';
}

// Build a RadioList from the provided options
function RadioList($fieldName, $dataArray, $fldIdx)
{
	global $data;
	$currentValue = $data["$fieldName$fldIdx"];
	//echo "Field $fieldName$fldIdx $currentValue<br>";
	foreach( $dataArray as $key=>$value )
	{
		//onBlur=\"CheckFields()\"
		echo "<label><input name=\"$fieldName$fldIdx\" id=\"$fieldName$fldIdx\" 
			onClick=\"ptClick(this)\"";
		if ( $value[0] == $currentValue ) echo " CHECKED ";
		echo " value=\"$value[0]\" type=\"radio\">$value[1]</label>";
		if ($value[0] == 'Other')
		{
			TextField("${fieldName}${fldIdx}Other", 100, 200, "CheckFields()", "other");
			/*
			echo "<input class=\"other\" size=\"100\" maxlength=\"200\" type=\"text\" 
				onBlur=\"CheckFields()\"
				id=\"${fieldName}${fldIdx}Other\"
				name=\"${fieldName}${fldIdx}Other\">";
			*/
		}
		echo "<br>\n";
	}
}


/*
 *
 * P R O P O S A L S
 *
 * Lets generate the 5 proposals
 *
*/

for ($idx=1; $idx <= $config->proposalCnt ; ++ $idx)
{
?>
<hr style="width: 100%; height: 2px;">
<!--
<textarea 
	rows="12" cols="80"
	style="width: 800px; height: 200px;" 
	name="DebugProp<?php echo $idx;?>" 
	id="DebugProp<?php echo $idx;?>" 
>Debug Msg Block</textarea>
-->
<h2 style="display:inline">PROPOSAL #<?php echo $idx;?> 
</h2>
<button type="button" id="UsePropBtn<?php echo $idx;?>" onClick="ShowUseProp(<?php echo $idx;?>);return false;">Use</button>
<div id="PropCtrl<?php echo $idx;?>" style="display:none;">
<button type="button" id="PropButton<?php echo $idx;?>" onClick="ShowHideProp(<?php echo $idx;?>);return false;">Hide</button>
<div id="PropMsg<?php echo $idx;?>" style="display:inline">unused</div>


<!-- This div will wrap the proposal input fields -->
<div class="prop" id="Proposal<?php echo $idx;?>" style=display:none>

<div class="q">Title of Presentation (including subtitle, if necessary): 
<div class="choice">
<?php TextField("Title$idx", 100, 100) ?>
</div></div>

<?php 
	if ($config->eventCode=='ROS') {
		RadioListOption('What TYPE of presentation is this (select one)?', 'PresentationType', $config->PresentationType, $idx);
	}
	RadioListOption('This workshop is appropriate for (select one):','TargetAudience', $config->Audience, $idx);
	RadioListOption('What is the AGE LEVEL appropriate for attendees of this workshop (select one)?','Age', $config->AgeGroup, $idx);
	if ($config->eventCode=='ROS') {
		RadioListOption('TIME preferences (select one)','TimePreference', $config->TimePreference, $idx);
		RadioListOption('SPACE requirements (select one):','SpacePreference', $config->SpacePreference, $idx);
	}
	RadioListOption('Is there a limit to the number of attendees for this presentation?', 'Limit', $config->AttendeeLimit, $idx);
	RadioListOption('Is there a materials fee to attend this presentation?','Fee', $config->MaterialsFee, $idx);
?>
<div class="q">Description of Presentation:
<div class="choice">(You may copy and paste into this box,the limit is still <?php echo $config->proposalTextSize;?>  characters.)
If you wish us to include special formatting in the program, use the following:<br>
For <i>italics</i>, put an underscore before and after the words to be italicized: _italic words_<br>
For <b>boldface</b> put a star before and after the words to be bolded: *boldface words*<br>
Please do not use all capital letters.<br>


<?php

	TextAreaWithCounter("Presentation$idx");
/*

<textarea 
	rows="12" cols="80"
	style="width: 800px; height: 200px;" 
	name="Presentation<?php echo $idx;?>" 
	id="Presentation<?php echo $idx;?>" 
	onkeydown="limitText(this.form.Presentation<?php echo $idx;?>,this.form.countdown<?php echo $idx;?>,<?php echo $proposalTextSize;?>);" 
	onkeyup="limitText(this.form.Presentation<?php echo $idx;?>,this.form.countdown<?php echo $idx;?>,<?php echo $proposalTextSize;?>);"></textarea><br>
	<font size="1">(Maximum characters: <?php echo $proposalTextSize;?>)<br>
	You have <input readonly="readonly" name="countdown<?php echo $idx;?>" size="3" value="<?php echo $proposalTextSize;?>" type="text"> characters left.</font>
*/
?>
</div></div></div>
<!-- PropCtrln --></div>
<?php
// Ok, this is the end of the Proposal Loop
//echo recaptcha_get_html($publickey);
}
//<div class="g-recaptcha" data-sitekey="6LfQqhUTAAAAAO28c7P6w5BS0Up-eE82FWgypC82"></div>
?>
<hr/>

<input value="Submit" type="Button" onClick="SubmitForm()">
</form></body></html>
