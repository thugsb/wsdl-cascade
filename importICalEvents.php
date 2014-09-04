<?php

if (!isset($_GET['user'])) {exit;}

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

    $xml = '<?xml version="1.0"?>' . "\n";

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
            $xml.='>'. str_replace("\\",'',htmlspecialchars($value)) . '</' . $propertyName . ">\n";
            // Original: $xml.='>'. htmlspecialchars($value) . '</' . $propertyName . ">\n";

        }

    }

    return $xml;

}

$icalendarData = file_get_contents("https://apply.slc.edu/manage/event/?user=".$_GET['user']."&output=ical");

print_r(iCalendarToXML($icalendarData));

?>