<?php
include "searchEngine.php";


function searchUI() 
{
	$response = null;
	if (isset($_POST['searchText'])) 
	{
		$keyword = $_POST['searchText'];
		try {
			$response = search($keyword);
		} 
		catch(Exception $e) {
			print $e -> getMessage();
		}
	}
	return $response;
}

function ifDateExists($theString)
{
	// check to see there are dates in the given string or not
	$pattern = '/[٠-٩]* (يناير|فبراير|مارس|أبريل|إبريل|مايو|يونيو|يونيه|يوليو|يوليه|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)، [٠-٩]*/';
	return (bool)preg_match($pattern, $theString, $matches,  PREG_OFFSET_CAPTURE);
}

function ifAtExists($theString)
{
	// check to see there @ exists
	return (bool)strpos($theString,'@');
	
}

function getContentArray()
{
	//get data from solr
	$contentArray=array();	
	$i=0;
	$results = searchUI();
	foreach ($results->response->docs as $doc)
	{	
		if ((ifDateExists((string)$doc->content[0]) && (ifAtExists((string)$doc->content[0]))))// validate good tweet
		{
			$contentArray[$i]= (string)$doc->content[0];
			$i++;
		}
	}
	return ($contentArray);	
}

function getCountArray($theArray)
{
	return count($theArray);
}

function printArray($theArray)
{
	foreach ($theArray as &$value) 
	{	
		echo $value. "<br /><br /><br /><br />";
	}
}

function getDateFromString($theString)
{
	// search for dates in the given string and return it
	$pattern = '/[٠-٩]* (يناير|فبراير|مارس|أبريل|إبريل|مايو|يونيو|يونيه|يوليو|يوليه|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)، [٠-٩]*/';
	preg_match($pattern, $theString, $matches,  PREG_OFFSET_CAPTURE);
		
	// the extracted date	
	return ($matches[0][0]);
	
}

function matchTwoDates($string1,$string2 )
{
	if (strcmp($string1,$string2)==0)
		return  true;
	else
		return false;
}

function getWriter($str)
{
	$from = "@";
	$to = " ";
	$sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
    	$writer = substr($sub,0,strpos($sub,$to));
        return $writer;
}
//function countDates($theDate)
//{
	//$i=0;
	//$theArray=getContentArray();
	//$theDate= trim($theDate);
//foreach ($theArray as &$value) 
	//{
		//if (matchTwoDates($value, $date))
			//i++;	
//}
	//return $i;	
//}


function my_gtw()
{
	// four parellel arrays
	$theArray= getContentArray(); // content array
	$theDateArray=array();	//date array	
	$theCountArray=array(); // count of date repeat array
	$spamWriterArray=array(); //array of spamming writers
	if($theArray != null)
	{
		for ($i=0; $i<count($theArray) ; $i++) // make date cout array
		{
			$theDateArray[$i]= getDateFromString($theArray[$i]);
		}
		 
		
		for ($i=0; $i<count($theDateArray) ; $i++) // make date cout array
		{
			$counter=0;
			for ($j=0 ; $j<count($theDateArray); $j++) 
			{
				if ($i!=$j)
					if(matchTwoDates((string)$theDateArray[$i], (string)$theDateArray[$j]))
						$counter ++;
			}
			$theCountArray[$i]=$counter;	
		}
		
		$count=0; //make spamming writer array
		for ($i=0;$i < count($theCountArray); $i++) // get the index of spammed
		{
			if ($theCountArray[$i] > 1)
			{
				$spamWriterArray[$count]=getWriter($theArray[$i]);	
				$count++;
			}	
		}
	if ($spamWriterArray != null)
		return $spamWriterArray;
	else
		return "There is no spammers for this product<br><br><br>";
	}		
}

?>


