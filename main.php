<?php

function queryString($medium, $titleType, $venue, $authorType, $pid){
    if ($venue != ""){
        return "//dblpperson/r/{$medium}[{$titleType}/text()=\"{$venue}\"]/{$authorType}[@pid=\"{$pid}\"]";
    }
    else {
        return "//dblpperson/r/{$medium}/{$authorType}[@pid=\"{$pid}\"]";
    }
}

function queryXML()
{
    $textinput = end($_GET);
    $nameCountArray = [];
    //cycle through each xml document with names given
    foreach ($_GET as $key => $value)
    {
        if ($key != "venue")
        {
            $countArray = [];
            $xmldoc = new DOMDocument();
            $xmldoc->load($key . ".xml");
            $xpath = new DOMXPath($xmldoc);
            // cycle through again for each name given
            foreach ($_GET as $key2 => $value2)
            {
                if ($key != "venue")
                {
                    $domList = $xpath->query(
                        queryString("article",  "journal", $textinput, "author", $value2)."|".
                        queryString("book",  "none", $textinput, "author", $value2)."|".
                        queryString("inproceedings",  "booktitle", $textinput, "author", $value2) ."|".
                        queryString("incollection",  "booktitle", $textinput, "author", $value2) ."|".
                        queryString("proceedings",  "booktitle", $textinput, "editor", $value2)
                    );
                    echo count($domList, COUNT_NORMAL);
                    echo " ";
                }
            }
        }
    }
}
//$i; $i < count($_Get,COUNT_NORMAL)-1; $i++



?>


<!DOCTYPE html>
<html lang="en">
<body>
<?php
queryXML();

?>
</body>
</html>
