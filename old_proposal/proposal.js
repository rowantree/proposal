/*
 * $Id: proposal.js 68 2016-02-28 22:10:19Z stephen $
 */

String.prototype.trim=function(){return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');};

var msg;

function limitText(limitField, limitCount, limitNum) 
{
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}

// Validate the form and if all is good then submit it otherwise prompt user to correct
function SubmitForm()
{
	/*
	if ( document.forms[0].recaptcha_response_field.value == '')
	{
		alert("Please enter the recaptcha information");
		document.forms[0].recaptcha_response_field.focus();
		return;
	}
	*/

	/*

	var id = document.getElementById('g-recaptcha');
	if ( grecaptcha.getResponse(id) == '' ) 
	{
		alert('Please enter the reCAPTCHA');
		return;
	}
	*/

	CheckFields();
	var form = document.forms[0];
	var errorCnt = 0;

	errorCnt = CheckFieldList(form,fieldList,errorCnt); 



	// See how many titles they filled in
	var usedProposalCnt = 0;
	for (nbr=1; nbr<=proposalCnt; ++nbr) 
	{
		if (document.getElementById("UsePropBtn" + nbr).innerHTML != 'Use')
		{
			++usedProposalCnt;
			if (document.getElementById("PropMsg"+nbr).innerHTML != 'complete')
			{
				alert("Proposal number " + nbr + " is not complete");
				return;
			}
		}
	}

	if ( usedProposalCnt < 1 )
	{
		alert("You must enter at least one proposal");
		return;
	}

	if ( errorCnt == 0 ) form.submit();
}

function CheckFieldList(form,fieldList,errorCnt)
{
	var newClassName = '';
	for ( var i in fieldList )
	{
		fieldInfo = fieldList[i];
		//alert("Checking " + fieldInfo.name);
		//

		var field = form[fieldInfo.name];
		if ( field == undefined )
		{
			alert("Unknown Field:" + fieldInfo.name);
			return;
		}
		if ( fieldInfo.type == 'radiobutton' )
		{

			// find the div element
			//if ( !confirm('Looking for ' + fieldInfo.name) ) exit();
			var el = document.getElementsByName(fieldInfo.name)[0];
			while( el.tagName != 'DIV') {
				//if ( !confirm(el.tagName) ) exit();
				el=el.parentNode;
			}
			el.className='choice';

			var value = -1;
			for (var i=0; i<field.length; ++i)
			{
				//alert(i);
				if ( field[i].checked )
				{
					value = field[i].value;
					break;
				}
			}
			if ( value == -1 )
			{
				if (errorCnt==0)
				{
					alert("Please select the " + fieldInfo.desc);
					field[0].focus();
				}
				el.className="error";
				++errorCnt;
			}
			if ( value == 'Other' )
			{
				// make sure the other field is filled in
				if ( form[fieldInfo.name + 'Other'].value.trim().length == 0 )
				{
					if ( errorCnt==0 )
					{
						alert("Please enter the text for " + fieldInfo.desc);
						form[fieldInfo.name + 'Other'].focus();
					}
					el.className="error";
					++errorCnt;
				}
			}
		}
		else if ( fieldInfo.type == 'other' )
		{
			// ignore
		}
		else {
			newClassName="";
			field.style.background='';
			if ( (field.value.trim()).length == 0 )
			{
				if (errorCnt==0) {
					alert("Please enter your " + fieldInfo.desc);
					field.focus();
				}
				newClassName="error";
				++errorCnt;
			}
			try {
				document.getElementById(fieldInfo.name).className = newClassName;
			}
			catch (err)
			{
				alert("Could not get that element:" + fieldInfo.name + ";\n" + err.message);
			}
		}
	}

	return errorCnt;
}




// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

// set the radio button with the given value as being checked
// do nothing if there are no radio buttons
// if the given value does not exist, all the radio buttons
// are reset to unchecked
function setCheckedValue(radioObj, newValue) {
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}

// use clicked on a radiobutton field; see if we should show the "other" field
function ptClick(field)
{
	var value = 'hidden';
	if (getCheckedValue(field)=="Other")
	{
		value = 'visible';
	}
	//alert(typeof(field.form[field.name + 'Other
	if ( typeof(field.form[field.name + 'Other']) != 'undefined' ) 
		field.form[field.name+'Other'].style.visibility=value;

	CheckFields();
}


// Check all the fields in the proposals and bios
function CheckFields()
{
	var idx;
	for (idx=1; idx<=3; ++idx) CheckBio(idx);
	for (idx=1; idx<=proposalCnt; ++idx) 
	{
		CheckProp(idx);
	}
}

// check the fields in the specified proposal
function CheckProp(nbr)
{
	// See if we need to handle this
	if (document.getElementById("UsePropBtn" + nbr).innerHTML == 'Use')
	{
		// then we are not using it, hide msg block and return
		//document.getElementById("DebugProp"+nbr).style.display = 'none';
		return;
	}
	//document.getElementById("DebugProp"+nbr).style.display = 'block';

	var idx;
	var el = document.getElementById("PropMsg"+nbr);
	msg = "";
	var usedFldCnt = 0;
	var fieldUsed;
	for (idx=0; idx<proposalFields.length; ++idx)
	{
		var field = proposalFields[idx];
		var fieldType = field.type;
		var fieldName = field.name + nbr;
		msg += "fld(" + fieldType +")" + fieldName + "=";
		if ( fieldType=='textbox')
		{
			fieldUsed =  isTextFieldUsed(fieldName);
		}
		else if (fieldType=='radiobutton') 
		{
			fieldUsed = isRadioButtonUsed(fieldName,msg);
		}
		else if (fieldType=='other')
		{
			// just ignore
		}
		else 
		{
			alert("Unknown Field Type '" + fieldType + "' while processing '" + fieldName + "'");
		}

		if ( fieldUsed )
		{
			++usedFldCnt;
			msg += "used;";
		}
		else 
		{
			msg += "unused;";
		}
		msg += "\n";
	}
	//alert("CheckProp #" + nbr + " FldCnt=" + usedFldCnt + " Total=" + proposalFields.length);
	if ( usedFldCnt == 0 ) 
		el.innerHTML = "unused";
	else if ( usedFldCnt == proposalFields.length )
		el.innerHTML = "complete";
	else el.innerHTML = "incomplete";
	//(document.forms[0])["DebugProp"+nbr].value = msg + "<br>usedFldCnt=" + usedFldCnt + " proposalFields.length=" + proposalFields.length;

}

// determin if the indicated bio is empty, incomplete, or complete
function CheckBio(nbr)
{
	// check the three fields: LegalName, ProgramName, bio
	var ln = isTextFieldUsed("LegalName" + nbr);
	var pn = isTextFieldUsed("ProgramName" + nbr);
	var bi = isTextFieldUsed("bio" + nbr);

	var el = document.getElementById("BioMsg"+nbr);

	if ( !ln && !pn && !bi )
	{
		el.innerHTML = "unused";
	} 
	else if ( ln && pn && bi )
	{
		el.innerHTML = "complete";
	}
	else 
	{
		el.innerHTML = "incomplete";
	}
}

function isRadioButtonUsed(fieldName)
{
	//console.log("isRadioButtonUser:" + fieldName);
	// need to see if we can find one of the buttons checked
	var field = (document.forms[0])[fieldName];
	if ( field === undefined ) {return true; }
	try {
	for( var i=0; i<field.length; ++i )
	{
		if ( field[i].checked ) 
		{
			msg += "[idx=" + i + ";value=" + field[i].value + ']';

			if ( field[i].value == 'Other')
			{
				// this is the other field so they need to have entered some text
				return (document.forms[0])[fieldName + 'Other'].value != "";
			}
			else return true;
		}
	}
	return false;
	}
	catch(err)
	{
		alert("caught:" + err);
		return true;
	}
}

function isTextFieldUsed(fieldName)
{
	try {
		return (document.forms[0])[fieldName].value != "";
	} catch (e) {
		alert("Can't find the value for field:" + fieldName);
	}
}

function ToggleBio(nbr)
{
	var form = document.forms[0];
	var field = form["BioRadioButton"+nbr];
	var div = document.getElementById("Bio"+nbr);

	//var lbl = "Proposal" + nbr;
	//div = document.getElementById(lbl);
	//alert(lbl + "=>" + div.id);

	div.style.display = field.checked ? 'block' : 'none';
}

function ShowHideBio(nbr)
{
	var el = document.getElementById("BioButton" + nbr);
	var label = el.innerHTML;
	var div = document.getElementById("Bio"+nbr);
	if ( label=='Show' )
	{
		el.innerHTML = 'Hide';
		div.style.display = 'block';
	}
	else {
		el.innerHTML = 'Show';
		div.style.display = 'none';
	}
	CheckFields();
	return false;
}

// show or hide the presentation control
function ShowUseProp(nbr)
{
	var el = document.getElementById("UsePropBtn" + nbr);
	var label = el.innerHTML;
	var div = document.getElementById("PropCtrl"+nbr);
	if ( label=='Use' )
	{
		el.innerHTML = 'Do Not Use';
		div.style.display = 'inline';
		// make sure the proposal section is display if it should be
		if ( document.getElementById("PropButton" + nbr).innerHTML=='Hide' )
			document.getElementById("Proposal" + nbr).style.display='block';
	}
	else {
		el.innerHTML = 'Use';
		div.style.display = 'none';
		// make sure the proposal section is hidden
		document.getElementById("Proposal" + nbr).style.display='none';
	}
	CheckFields();
	return false;
}

function ShowUseBio(nbr)
{
	var el = document.getElementById("UseBioBtn" + nbr);
	var label = el.innerHTML;
	var div = document.getElementById("BioCtrl"+nbr);
	if ( label=='Use' )
	{
		el.innerHTML = 'Do Not Use';
		div.style.display = 'inline';
		// make sure the proposal section is display if it should be
		if ( document.getElementById("BioButton" + nbr).innerHTML=='Hide' )
			document.getElementById("bio" + nbr).style.display='block';
	}
	else {
		el.innerHTML = 'Use';
		div.style.display = 'none';
		// make sure the proposal section is hidden
		document.getElementById("bio" + nbr).style.display='none';
	}
	CheckFields();
	return false;
}

// show or hide the indicated presentation
function ShowHideProp(nbr)
{
	var el = document.getElementById("PropButton" + nbr);
	var label = el.innerHTML;
	var div = document.getElementById("Proposal"+nbr);
	if ( label=='Show' )
	{
		el.innerHTML = 'Hide';
		div.style.display = 'block';
	}
	else {
		el.innerHTML = 'Show';
		div.style.display = 'none';
	}
	CheckFields();
	return false;
}


function ToggleArea(chkBox, elementId)
{
	var el = document.getElementById(elementId);
	el.style.display = chkBox.checked ? 'block' : 'none';
}
