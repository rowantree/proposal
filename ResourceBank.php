<html>
<head>

<title>
	EarthSpirit Resource Bank
</title>

<style>
	th { text-align:right; }
	.rowhr { background: black; height:1px; }
</style>

<script>
function Toggle(chkBox, detailRow)
{
	var checked = document.forms[0][chkBox].checked;
	var el = document.getElementById(detailRow);
	el.style.display = checked ?  '' : 'none';
}
</script>

</head>

<body>

I can offer EarthSpirit my help with:
<form>
<table>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Event logistics/inventory</th>
<td><input type="checkbox"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Grant-Writing</th>
<td><input type="checkbox"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Promotions</th>
<td><input type="checkbox"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Graphic Design</th>
<td><input type="checkbox"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Regular maintenance of outdoor<br> and/or indoor conference space</th>
<td><input type="checkbox"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Coordination of EarthSpirit classes, workshops,<br> and/or presentations in other geographical areas</th>
<td><input name="chkClasses" type="checkbox" onClick="Toggle('chkClasses','ClassesDetail')"></td>
</tr>

<tr id="ClassesDetail" style="display:none">
<th></th>
<td>where?<input type="text"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>Other skills you can offer</th>
<td><input name="chkSkills" type="checkbox" onClick="Toggle('chkSkills','SkillsDetail')"></td>
</tr>


<tr id="SkillsDetail" style="display:none">
<th></th>
<td><textarea rows="20" cols="60"></textarea>
</td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<th>*Name</th>
<td><input type="text"></td>
</tr>


<tr>
<th>*Best Email</th>
<td><input type="text"></td>
</tr>

<tr>
<th>*Best phone</th>
<td><input type="text"></td>
</tr>

<tr>
<th>*Zipcode</th>
<td><input type="text"></td>
</tr>

<tr class="rowhr"><td colspan="2"></td></tr>

<tr>
<td colspan="2" align="center"><button>Submit</button></td>
</tr>


</table>

</body>

</form>
