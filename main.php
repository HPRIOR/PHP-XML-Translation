<?php
/*
 * I have changed the names of the xml files to make the key-values of the associative array more conducive to
 * the solution.
 */

// returns query string from arguments. Conditional checks for venue specification
function queryString($medium, $titleType, $venue, $authorType, $pid){
    if ($venue != ""){
        return "//dblpperson/r/{$medium}[{$titleType}/text()=\"{$venue}\"]/{$authorType}[@pid=\"{$pid}\"]";
    }
    else {
        return "//dblpperson/r/{$medium}/{$authorType}[@pid=\"{$pid}\"]";
    }
}

// returns the names of selected academics, or order so that they can be used to generate html table
function getNameArray(){
    $nameArray = [];
    foreach($_GET as $key => $value){
        if ($key != "venue") {
            $nameArray[] = $key;
        }
    }
    return $nameArray;
}

// returns a 2D Array. First loop iterates through each xml doc, the next queries each xml with each pid value
function countArray()
{
    $textInput = trim(end($_GET));
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
                   $evaluate = $xpath->evaluate("count(".
                        queryString("article",  "journal", $textInput, "author", $value2)."|".
                        queryString("book",  "none", $textInput, "editor", $value2)."|".
                        queryString("book",  "none", $textInput, "author", $value2)."|".
                        queryString("inproceedings",  "booktitle", $textInput, "author", $value2) ."|".
                        queryString("incollection",  "booktitle", $textInput, "author", $value2) ."|".
                        queryString("proceedings",  "booktitle", $textInput, "editor", $value2).")"
                    );
                   // appends the result of each query to the nested array
                    $nestedArray[] = $evaluate;
                }
            }
            // appends nested array to the returned array of the function
            $returnArray[] = $nestedArray;
            // resets the nested array for the next loop
            $nestedArray = [];
        }
    }
    return $returnArray;
}


// generates table from 2D array and name array
function createTable(){
    $nestedArray = countArray();
    $count = count($nestedArray, COUNT_NORMAL);
    $nameArray = getNameArray();
    if ($count != 0){
        echo "<table><tr><th> -  </th>";
        foreach ($_GET as $key => $value) {
            // adds names to top of the table
            if ($key != "venue") echo "<th>{$key}</th>";
        }
        echo "</tr>";
        for ( $i = 0 ; $i < $count; $i++){
            // add names to first column
            echo "<tr><td><b>".$nameArray[$i]."</b></td>";
            for ( $j = 0 ; $j < $count; $j++){
                // populate table with results in nested array
                echo "<td>".$nestedArray[$i][$j]."</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    // shows error message if no authors selected
    else echo "No authors/editors selected";

}

// returns header based on the value of text box in HTML
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
            <?php echo createHeader() ?>
        </h1>
        <?php createTable(); ?>
    </body>
</html>
