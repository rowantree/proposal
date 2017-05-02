<?php
	session_start();

	$data = array();


	$data['LegalName'] = 'Stephen Morley';
	$data['ProgramName'] = 'Stephen';
	$data['email'] = 'stephen@morley.co';
	$data['phone'] = '203.733.2578';
	$data['LegalName'] = 'Stephen Morley';
	$data['unavailable'] = 'none';
	$data['biography'] = 'This is the contents of my biography';

	$_SESSION['RegData'] = $data;

	Include("proposal.php");


?>
