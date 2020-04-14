<?php

function queryString($medium, $titleType, $venue, $authorType, $pid){
    //change query - think 'book' medium type is the problem
    if ($venue != ""){
        return "//dblpperson/r/{$medium}[{$titleType}/text()=\"{$venue}\"]/{$authorType}[@pid=\"{$pid}\"]";
    }
    else {
        return "//dblpperson/r/{$medium}/{$authorType}[@pid=\"{$pid}\"]";
    }
}

function getNameArray(){
    $nameArray = [];
    foreach($_GET as $key => $value){
        if ($key != "venue") {
            $nameArray[] = $key;
        }
    }
    return $nameArray;
}

function countArray()
{
    $textInput = end($_GET);
    $returnArray = [];
    $nestedArray = [];
    //cycle through each xml document with names given
    foreach ($_GET as $key => $value) {
        if ($key != "venue") {
            $xmldoc = new DOMDocument();
            $xmldoc->load($key . ".xml");
            $xpath = new DOMXPath($xmldoc);
            // cycle through again for each name given
            foreach ($_GET as $key2 => $value2) {
                if ($key2 != "venue"){
                    $domList = $xpath->query(
                        queryString("article",  "journal", $textInput, "author", $value2)."|".
                        queryString("book",  "none", $textInput, "author", $value2)."|".
                        queryString("inproceedings",  "booktitle", $textInput, "author", $value2) ."|".
                        queryString("incollection",  "booktitle", $textInput, "author", $value2) ."|".
                        queryString("proceedings",  "booktitle", $textInput, "editor", $value2)
                    );
                    $nestedArray[] = count($domList, COUNT_NORMAL);
                }
            }
            $returnArray[] = $nestedArray;
            $nestedArray = [];
        }
    }
    return $returnArray;
}

function createTable(){
    $nestedArray = countArray();
    $count = count($nestedArray, COUNT_NORMAL);
    $nameArray = getNameArray();
    if ($count != 0){
        echo "<table>";
        echo "<tr>";
        echo "<th> -  </th>";
        foreach ($_GET as $key => $value) {
            if ($key != "venue") echo "<th>{$key}</th>";
        }
        echo "</tr>";
        for ( $i = 0 ; $i < $count; $i++){
            echo "<tr>";
            echo "<td>".$nameArray[$i]."</td>";
            for ( $j = 0 ; $j < $count; $j++){
                echo "<td>";
                echo $nestedArray[$i][$j];
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    else echo "No authors/editors selected";

}

function createHeader(){
    $venue = $_GET["venue"];
    if ($venue == ""){
        return "Publication records for all venues";
    }
    else{
        return "Publication records for {$venue}";
    }
}

?>


<!DOCTYPE html>
<style>
    table, th, td {
        border: 1px solid black;
    }
</style>
<html lang="en">
    <body>
        <h1>
            <?php
            echo createHeader()
            ?>
        </h1>

            <?php
            createTable();
            ?>

    </body>
</html>
