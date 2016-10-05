	
use strict;
use warnings;

use Text::CSV::Encoded;
use DBI;

##my $csv = Text::CSV->new();
#my $csv = Text::CSV::Encoded->new( { encoding_in => 'iso-8859-1' });
my $csv = Text::CSV::Encoded->new( { encoding_in => 'UTF-8' }) or die;
my $file = "data\\ROS_2016_proposal_original.csv";
my $DbConn = DBI->connect("DBI:mysql:rowan_ritesregistration:ustwpres404", "root", "vepru6Wa", {AutoCommit => 0}) or die;



my $insert_proposal = $DbConn->prepare("INSERT INTO proposal
        (event_id, legal_name, program_name, email_address, telephone_number, unavailable_times, biography, when_arriving, last_attended ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ? )");


my $insert_detail = $DbConn->prepare("INSERT INTO proposal_detail 
    ( proposal_id, title, presentation_type, presentation_type_other, target_audience, age, age_other, time_preference, time_preference_other, space_preference, space_preference_other, participant_limit, participant_limit_detail, fee, fee_detail, presentation)
    VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");



my (@fields, $field, %fieldIdx, $proposal_id);

open(my $data, "<$file") or die;

my $line = <$data>;
print $line;
chomp $line;
if ($csv->parse($line))
{
	@fields = $csv->fields();
}

my $cnt = @fields - 1;
for( my $idx=0; $idx<$cnt; ++$idx )
{
	print "[$idx] ", $fields[$idx], "\n";
	$fieldIdx{$fields[$idx]} = $idx;
}

while( my $line = <$data>)
{
	chomp $line;
	if ($csv->parse($line))
	{
		@fields = $csv->fields();
		$insert_proposal->execute(1,
			$fields[$fieldIdx{"Legal Name"}],
			$fields[$fieldIdx{"Program Name"}],
			$fields[$fieldIdx{"Email Address"}],
			$fields[$fieldIdx{"Telephone Number"}],
			$fields[$fieldIdx{"Unavailable Times"}],
			$fields[$fieldIdx{"Biography"}],
			$fields[$fieldIdx{"When arriving"}],
			$fields[$fieldIdx{"Attended Rites of Spring"}]
		);
		$proposal_id = $DbConn->last_insert_id( undef, undef, undef, undef );
		print $fields[$fieldIdx{"Legal Name"}], "=>$proposal_id\n";
	}
	else {
		print "Could not parse that line: $line\n";
	}

	for(my $idx=1; $idx<5; ++$idx)
	{
		my $title = $fields[$fieldIdx{"Title  $idx"}];
		if ( $title ne '' )
		{
			print "....[$idx] $title\n";
			$insert_detail->execute($proposal_id,
				$title,
				$fields[$fieldIdx{"Presentation Type  $idx"}],
				$fields[$fieldIdx{"Presentation Type Other  $idx"}],
				$fields[$fieldIdx{"Target Audience  $idx"}],
				$fields[$fieldIdx{"Age  $idx"}],
				$fields[$fieldIdx{"Age Other  $idx"}],
				$fields[$fieldIdx{"Time Preference  $idx"}],
				$fields[$fieldIdx{"Time Preference Other  $idx"}],
				$fields[$fieldIdx{"Space Preference  $idx"}],
				$fields[$fieldIdx{"Space Preference Other  $idx"}],
				$fields[$fieldIdx{"Limit  $idx"}] eq 'yes' ? 1 : 0,
				$fields[$fieldIdx{"Limit Detail  $idx"}],
				$fields[$fieldIdx{"Fee  $idx"}] eq 'yes' ? 1 : 0,
				$fields[$fieldIdx{"Fee Detail  $idx"}],
				$fields[$fieldIdx{"Presentation  $idx"}]
			);
		}
	}

}

$DbConn->commit();


