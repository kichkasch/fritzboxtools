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

$accountIndex = "0";		// Default line to use for dialing out - count starting with 0

$davUrl = "http://FULL_URL";
$username = "user";
$password = "***";
$filename = "FritzOut.xml";

$localCountryCode = '+49'; // configure this in order to enable the GXP phone to pick up names from address book for incoming calls

/* 
entries may come with several phone numbers attached to one name; so we need to 
resolve types of phones some how; I did this by suffixing a single letter in brackets to
the name of the entry - you may configure this here.
Take care of the leading space - otherwise the suffix is directly attached to the name - not nice.
*/ 
$homePhoneSuffix = ' [H]';
$workPhoneSuffix = ' [W]';
$mobilePhoneSuffix = ' [M]';

/* !--
end configuration
*/
?>