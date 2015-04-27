<?php
/*
Carddav to Fritzbox Addressbook XML (Backup) Converter
by Michael Pilgermann (kichkasch@gmx.de)
(origionally based on database version from Shane Steinbeck http://www.steinbeckconsulting.com)
Copyright (C) 2015 Michael Pilgermann

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

*/

header("Content-type: text/xml");

require 'carddav.php'; 		/* https://github.com/christian-putzke/CardDAV-PHP */
require 'vcard.php';			/* https://github.com/nuovo/vCard-parser */
require 'config.php';

$vcfIDLength = 36;

$xml_output = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
$xml_output .= "<phonebooks>\n";
$xml_output .= "<phonebook>\n";


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
	
 	$xml_output .= "\t<contact>\n";
	$xml_output .= "\t\t<category>0</category>\n";
	$xml_output .= "\t\t<person><realName>" . $vCard -> fn[0] . "</realName></person>\n";

	$xml_output .= "\t\t<telephony>\n";
	foreach ($vCard -> tel as $telEntry)
	{
			/*$xml_output .= "\t\t\t<number type=\"home\" vanity=\"\" prio=\"0\">" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . ", " . print_r($telEntry['Type'], true) . "</number>\n";*/
		/*$xml_output .= print_r($telEntry, true);*/
		if ($telEntry['Type'][0] == "cell") {
			$xml_output .= "\t\t\t<number type=\"mobile\" vanity=\"\" prio=\"0\">" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . "</number>\n";
		} elseif($telEntry['Type'][0] == "home") {
			$xml_output .= "\t\t\t<number type=\"home\" vanity=\"\" prio=\"0\">" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . "</number>\n";
		} elseif($telEntry['Type'][0] == "work") {
			$xml_output .= "\t\t\t<number type=\"work\" vanity=\"\" prio=\"0\">" . noLocalCountryCode($telEntry['Value'], $localCountryCode) . "</number>\n";
		}
	}
	$xml_output .= "\t\t</telephony>\n";

	$xml_output .= "\t\t<services />\n";
	$xml_output .= "\t\t<setup />\n";
	$xml_output .= "\t\t<uniqueid>" . $i . "</uniqueid>\n";
	$xml_output .= "\t</contact>\n";
	$vcfPos = strpos($data, ".vcf", $offset = $vcfPos+1);
	$vcfPos = strpos($data, ".vcf", $offset = $vcfPos+1); /* 2x; einen muessen wir ueberspringen - wegen href im HTML */
	$i+=1;
}

$xml_output .= "</phonebook>";
$xml_output .= "</phonebooks>";

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
