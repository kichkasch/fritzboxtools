<?php
/*
Carddav to GXP Addressbook XML Converter
by Michael Pilgermann (kichkasch@gmx.de)
(origionally based on database version from Shane Steinbeck http://www.steinbeckconsulting.com)
Copyright (C) 2016 Michael Pilgermann

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

*/

header("Content-type: text/xml");

require 'carddav.php'; 		/* https://github.com/christian-putzke/CardDAV-PHP */
require 'vcard.php';			/* https://github.com/nuovo/vCard-parser */
require 'config.php';

// GXP settings
$accountIndex = "0";		// Default line to use for dialing out - count starting with 0
$localCountryCode = '+49'; // configure this in order to enable the GXP phone to pick up names from address book for incoming calls

$vcfIDLength = 36;

$xml_output = "<?xml version=\"1.0\"?>\n";
$xml_output .= "<AddressBook>\n";


/* jetzt wird es ekelig; PROP gibt leider einen 403 auf meiner Synology */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $davUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
$data = curl_exec($ch);
curl_close($ch);
/* Ende ekelig */

$carddav = new carddav_backend($davUrl);
$carddav->set_auth($username, $password);

$vcfPos = strpos($data, ".vcf", $offset = null);
$i = 0;
while($vcfPos) 
{
	$vcfID = substr($data, $vcfPos - $vcfIDLength, $vcfIDLength);
	$vCard = new vCard(false, $carddav->get_vcard($vcfID));
	
	$nameArray = $vCard -> n[0];
	$xml_output .= "\t<Contact>\n";
	$xml_output .= "\t\t<LastName>" . $nameArray['LastName'] . "</LastName>\n";
	$xml_output .= "\t\t<FirstName>" . $nameArray['FirstName'] . "</FirstName>\n";

	foreach ($vCard -> tel as $telEntry)
	{
		if ($telEntry['Type'][0] == "cell") {
			$xml_output .= "\t\t\t<Phone type=\"Mobile\">\n\t\t\t\t<phonenumber>" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . "</phonenumber>\n";
		} elseif($telEntry['Type'][0] == "home") {
			$xml_output .= "\t\t\t<Phone type=\"Home\">\n\t\t\t\t<phonenumber>" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . "</phonenumber>\n";
		} else {
			$xml_output .= "\t\t\t<Phone type=\"Work\">\n\t\t\t\t<phonenumber>" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . "</phonenumber>\n";
		}
		$xml_output .= "\t\t\t\t<accountindex>" . $accountIndex . "</accountindex>\n";
		$xml_output .= "\t\t\t</Phone>\n";
	}

	$xml_output .= "\t</Contact>\n";
	$vcfPos = strpos($data, ".vcf", $offset = $vcfPos+1);
	$vcfPos = strpos($data, ".vcf", $offset = $vcfPos+1); /* 2x; einen muessen wir ueberspringen - wegen href im HTML */
	$i+=1;
}


$xml_output .= "</AddressBook>";
$fp = fopen('gs_phonebook-dyn.xml', 'wb');
fwrite($fp, $xml_output);
fclose($fp);
print($xml_output);


function noLocalCountryCode($number, $localCountryCode)
{
$pos = strpos($number, $localCountryCode);
if (!strcmp($pos,'0'))
{
        return '0' . substr($number, 3);
} else {
// replace leading '+' by double zero
        if (!strcmp(strpos($number, '+'), '0'))
        {
                return '00' . substr($number, 1);
        } else {
                return $number;
        }
}
}

?>
