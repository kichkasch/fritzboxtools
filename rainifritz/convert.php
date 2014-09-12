<?php
header("Content-type: text/xml");

// check there are no errors
if($_FILES['csv']['error'] == 0){
    $name = $_FILES['csv']['name'];
    $ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
    $type = $_FILES['csv']['type'];
    $tmpName = $_FILES['csv']['tmp_name'];

    // check the file is a csv
    if($ext === 'csv'){
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            // necessary if a large csv file
            set_time_limit(0);

	    $xml_output = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
	    $xml_output .= "<phonebooks>\n";
	    $xml_output .= "<phonebook>\n";

            $row = 0;
            while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // number of fields in the csv
		if ($row > 0) {
		        $num = count($data);

			$xml_output .= "\t<contact>\n";
			$xml_output .= "\t\t<category>0</category>\n";
			$xml_output .= "\t\t<person><realName>" . $data[0] . " " . $data[1] . "</realName></person>\n";
			$xml_output .= "\t\t<telephony>\n";
			if ($data[8]) // if home phone is set
			{ 
			    $xml_output .= "\t\t\t<number type=\"home\" vanity=\"\" prio=\"0\">" . $data[8] . "</number>\n";
			}
			if ($data[7]) // if work phone number is set
			{
			    $xml_output .= "\t\t\t<number type=\"work\" vanity=\"\" prio=\"0\">" . $data[7] . "</number>\n";
			}
			if ($data[11]) // if mobile phone is set
			{
			    $xml_output .= "\t\t\t<number type=\"mobile\" vanity=\"\" prio=\"0\">" . $data[11] . "</number>\n";
			} 
			$xml_output .= "\t\t</telephony>\n";
			$xml_output .= "\t\t<services />\n";
			$xml_output .= "\t\t<setup />\n";
			$xml_output .= "\t\t<uniqueid>" . $i . "</uniqueid>\n";
			$xml_output .= "\t</contact>\n";
		}
                // inc the row
                $row++;
            }
            fclose($handle);
	    $xml_output .= "</phonebook>";
	    $xml_output .= "</phonebooks>";

	    $fp = fopen('gs_phonebook-dyn.xml', 'wb');
	    fwrite($fp, $xml_output);
	    fclose($fp);
	    print($xml_output);
        }
    }
}
?>
