<?php

return [
	'input' => [
		'optin' => "",
	],
	'introduction' => [
		'1' => "introduction(1)",
		'2' => "introduction(2)",
		'3' => "introduction(3)",
		'4' => "introduction(4)",
	],
	'processing' => "Please stand by. Processing...",
	'processed' => "Invitation has been sent.",

	'fail' => "Something is wrong. Please notify the system administrator.",
];

function introduction($index)
{
	$text = [
			[],
			[
"
Welcome to the realm of automated
private messaging!

The system is designed to provide
you with:

- information you need
know the conditions of the geo-politcal 
terrain;
- online transmission of reward such as
cellphone credits;
- data gathering tools for feedback and 
analysis.
"
			],
			[
<<<INTRO2
The system capable of guiding
you in conducting surveys as well
as answering questions and task checklist.
It uses Artifical Intelligence and 
Natural Language Processing technologies
to pinpoint the intention in each 
conversation.
INTRO2
			],
			[
<<<INTRO3
The system can send messages to millions
at no extra cost. It leverages on the 
robust systems of messenger applications
- and the best part is you can send riders
such as marketing collaterals.
INTRO3
			],
			[
<<<INTRO4
The system employs One-Time PIN (OTPs) to 
authenticate face-to-face transactions. It
can even ask for the exact location of the 
subscriber for an even granular provisioning
of services. 
INTRO4
			],
	];

	return $text[$index]; 
}