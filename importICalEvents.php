<?php
date_default_timezone_set('America/New_York');

if (!isset($_GET['user'])) {exit;}

// From https://code.google.com/p/ics-parser/source/browse/trunk/class.iCalReader.php
function iCalDateToUnixTimestamp($icalDate) 
    { 
        $icalDate = str_replace('T', '', $icalDate); 
        $icalDate = str_replace('Z', '', $icalDate); 

        $pattern  = '/([0-9]{4})';   // 1: YYYY
        $pattern .= '([0-9]{2})';    // 2: MM
        $pattern .= '([0-9]{2})';    // 3: DD
        $pattern .= '([0-9]{0,2})';  // 4: HH
        $pattern .= '([0-9]{0,2})';  // 5: MM
        $pattern .= '([0-9]{0,2})/'; // 6: SS
        preg_match($pattern, $icalDate, $date); 

        // Unix timestamp can't represent dates before 1970
        if ($date[1] <= 1970) {
            return false;
        } 
        // Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow
        // if 32 bit integers are used.
        $timestamp = mktime((int)$date[4], 
                            (int)$date[5], 
                            (int)$date[6], 
                            (int)$date[2],
                            (int)$date[3], 
                            (int)$date[1]);
        return  $timestamp;
    }

// Function found at http://evertpot.com/248/ (modified, see below)
function iCalendarToXML($icalendarData) {

    // Detecting line endings
    if (strpos($icalendarData,"\r\n")) $lb = "\r\n";
    elseif (strpos($icalendarData,"\n")) $lb = "\n";
    else $lb = "\r\n";

    // Splitting up items per line
    $lines = explode($lb,$icalendarData);

    // Properties can be folded over 2 lines. In this case the second
    // line will be preceeded by a space or tab.
    $lines2 = array();
    foreach($lines as $line) {

        if ($line[0]==" " || $line[0]=="\t") {
            $lines2[count($lines2)-1].=substr($line,1);
            continue;
        }

        $lines2[]=$line;

    }

    $xml = '<?xml version="1.0" ""?>' . "\n";

    $spaces = 0;
    foreach($lines2 as $line) {

        $matches = array();
        // This matches PROPERTYNAME;ATTRIBUTES:VALUE
        if (preg_match('/^([^:^;]*)(?:;([^:]*))?:(.*)$/',$line,$matches)) {
            $propertyName = strtoupper($matches[1]);
            $attributes = $matches[2];
            $value = $matches[3];

            // If the line was in the format BEGIN:COMPONENT or END:COMPONENT, we need to special case it.
            if ($propertyName == 'BEGIN') {
                $xml.=str_repeat(" ",$spaces);
                $xml.='<' . strtoupper($value) . ">\n";
                $spaces+=2;
                continue;
            } elseif ($propertyName == 'END') {
                $spaces-=2;
                $xml.=str_repeat(" ",$spaces);
                $xml.='</' . strtoupper($value) . ">\n";
                continue;
            }

            $xml.=str_repeat(" ",$spaces);
            $xml.='<' . $propertyName;
            if ($attributes) {
                // There can be multiple attributes
                $attributes = explode(';',$attributes);
                foreach($attributes as $att) {

                    list($attName,$attValue) = explode('=',$att,2);
                    $xml.=' ' . $attName . '="' . htmlspecialchars($attValue) . '"';

                }
            }

            // This line modified to remove escaping-backslashes
            $xml.='>';
            if ($propertyName == 'DTSTART' || $propertyName == 'DTEND' || $propertyName == 'DTSTAMP') {
              $xml.=iCalDateToUnixTimestamp($value)*1000;
            } else {
              $xml.=str_replace("\\",'',htmlspecialchars($value));
            }
            $xml.='</' . $propertyName . ">\n";
            // Original: $xml.='>'. htmlspecialchars($value) . '</' . $propertyName . ">\n";

        }

    }

    return $xml;

}

$icalendarData = file_get_contents("https://apply.slc.edu/manage/event/?user=".$_GET['user']."&output=ical");

print_r(iCalendarToXML($icalendarData));

?>