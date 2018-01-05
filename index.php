<?php
include "searchEngine.php";

ini_set('display_errors', 'On');
error_reporting(0);
error_reporting(E_ALL);
error_reporting(0);	
//global variable

class Tweets 
{
   //variables
	public $contents=array();
	public $dates=array();
	public $titles=array();
	public $urls=array();
	public $spammers_gtw= array();
	public $spammers_gcs= array();
	public $spammers_gedft= array();
	public $spammers_ics= array();
	public $spammers_ieft=array();
	public $spammers_imc=array();
	public $firstDate=null;
	public $endDate=null;
	
   //------------------------------------------    	
   //constructor
	function Tweets() 
	{
		if (isset($_POST['searchText'])) // get input from textbox and check it has data
		{
			$keyword = $_POST['searchText'];
			try {
				$response = search($keyword);// query from solr
			} 
			catch(Exception $e) {
				print $e -> getMessage();
			}
		}

		$this->setArrays($response->response->docs );// get all the tweets
	}// end of constructor
    //--------------------------------------------------------------------
	

    // get all good tweets from solr AND SET THEM IN THE FOUR ARRAYS contents, anchors, titles, urls	
	function setArrays($docs)// work on all tweets
	{
		$theArray=array();
		$i=0;
		foreach ($docs as $doc)//loop in each document
		{	
			if ($this->setContents($doc->content[0]) <> null)// validate good tweet
			{
				if ($this->isDate($this->mergeAnchors($doc->anchor)) <> null)
				{				
					$this->contents[$i]= $this->setContents($doc->content[0]);
					$this->dates[$i]=$this->setDate($this->mergeAnchors($doc->anchor));
					$this->titles[$i]= $doc->title[0];
					$this->urls[$i]= $doc->url[0];
					$i++;
				}
			}
		}
	}
    //------------------------------------------------------------------------
  
    //helps getContents to filter each tweet to get real tweets and get rid of extra words
	function setContents($value)//works on only one tweet
	{
		$value =  strstr($value,'"');// remove what is before the first "
		$value = substr($value,1);// get the first letter after "
		$end=strcspn($value,'"');// count the number of letters until the next "
		$value = trim(substr($value,0,$end)); // remove the text after the second "
		
		if (trim($value)=="الرمز")// if it contains only this word, it is junk tweet
			$value=null;
		
		if (isset($value))
			$value= trim($value);// get rid of extra spaces
	
		return ($value);
	}
   //-----------------------------------------------------------------------------
	// help setdates by merging all the anchors of a tweet
	function mergeAnchors($anchors)// merge the anchors of a tweet in one string
	{
		if ($anchors == null)
			return null;
		else		
		{
			foreach ($anchors as $anchor)	
				$mystring = $mystring .(string)$anchor. " ";// get all anchors of a tweet
			
			if (strpos($mystring,'تجاهل') !== false)
				$mystring = null;	
		}
		return  $mystring;
	}	

  //---------------------------------------------------------------------------------
	// help setdates by checking first is there is a date
	function isDate($theString)
	{	//check  for three formats of dates
		$pattern1 = '/[٠-٩]* (يناير|فبراير|مارس|أبريل|إبريل|مايو|يونيو|يونيه|يوليو|يوليه|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)، [٠-٩]*/';
		$pattern2 = '/[٠-٩]* ';
		$pattern2 .= '(يناير|فبراير|مارس|أبريل|إبريل|مايو|يونيو|يونيه|يوليو|يوليه|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)/';
		$pattern3 = '/([0-9])* (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ([0-9])*/';
 
		if ($theString== null)
			return false;
		else if (preg_match($pattern1, $theString, $matches,  PREG_OFFSET_CAPTURE) == true)
			return true;
		else if (preg_match($pattern2, $theString, $matches,  PREG_OFFSET_CAPTURE) == true)	
			return true;	
		else if (preg_match($pattern3, $theString, $matches,  PREG_OFFSET_CAPTURE) == true)
			return true;
		else
			return false;
	}// end isDate
  //----------------------------------------------------------------------------
	// set the date array of all the tweets
	function setDate($theString)
	{
		//get the date after searching for three formats of dates
		$pattern1 = '/[٠-٩]* (يناير|فبراير|مارس|أبريل|إبريل|مايو|يونيو|يونيه|يوليو|يوليه|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)، [٠-٩]*/';
		$pattern2 = '/[٠-٩]* ';
		$pattern2 .= '(يناير|فبراير|مارس|أبريل|مايو|يونيو|يونيه|يوليو|يوليه|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)/';
		$pattern3 = '/[0-9]* (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) [0-9]*/';
 		$flag=false;

		if (preg_match($pattern1, $theString, $matches,  PREG_OFFSET_CAPTURE) == true)
		{
			$flag= true;
			$matches[0][0] =str_replace('،', '', $matches[0][0]);
		}
		else if (preg_match($pattern2, $theString, $matches,  PREG_OFFSET_CAPTURE) == true)	
		{
			$matches[0][0] .= ' ٢٠١٦';	
			$flag= true;	
		}
		else if (preg_match($pattern3, $theString, $matches,  PREG_OFFSET_CAPTURE) == true)
			$flag= true;
		else 
			$flag= false;
		
		// change arabic date to english
		$matches[0][0]=str_replace('يناير', 'Jan', $matches[0][0]);	
		$matches[0][0]=str_replace('فبراير', 'Feb', $matches[0][0]);
		$matches[0][0]=str_replace('مارس', 'Mar', $matches[0][0]);
		$matches[0][0]=str_replace('أبريل', 'Apr', $matches[0][0]);
		$matches[0][0]=str_replace('ابريل', 'Apr', $matches[0][0]);
		$matches[0][0]=str_replace('مايو', 'May', $matches[0][0]);
		$matches[0][0]=str_replace('يونيو', 'Jun', $matches[0][0]);
		$matches[0][0]=str_replace('يوليو', 'Jul', $matches[0][0]);
		$matches[0][0]=str_replace('يوليه', 'Jul', $matches[0][0]);
		$matches[0][0]=str_replace('أغسطس', 'Aug', $matches[0][0]);
		$matches[0][0]=str_replace('سبتمبر', 'Sep', $matches[0][0]);
		$matches[0][0]=str_replace('أكتوبر', 'Oct', $matches[0][0]);
		$matches[0][0]=str_replace('نوفمبر', 'Nov', $matches[0][0]);
		$matches[0][0]=str_replace('ديسمبر', 'Dec', $matches[0][0]);

		$matches[0][0]=str_replace('٠', '0', $matches[0][0]);
		$matches[0][0]=str_replace('١', '1', $matches[0][0]);
		$matches[0][0]=str_replace('٢', '2', $matches[0][0]);
		$matches[0][0]=str_replace('٣', '3', $matches[0][0]);
		$matches[0][0]=str_replace('٤', '4', $matches[0][0]);
		$matches[0][0]=str_replace('٥', '5', $matches[0][0]);
		$matches[0][0]=str_replace('٦', '6', $matches[0][0]);
		$matches[0][0]=str_replace('٧', '7', $matches[0][0]);
		$matches[0][0]=str_replace('۸', '8', $matches[0][0]);
		$matches[0][0]=str_replace('۹', '9', $matches[0][0]);


		// change all in one format
		
		$matches[0][0]= date("Y-M-d", strtotime($matches[0][0]));

		if ($flag==true)
			return $matches[0][0];
	}// end setDate



  //----------------------------------------------------------------
  // end of constructor helping functions 
  //----------------------------------------------------------------

  //------------------------------------------------------------------
  // -------------------gtw----------------------------------------
  //---------------------------------------------------------------

 	// helping function to gtw
	function matchStrings($string1,$string2)
	{
		if (strcmp($string1,$string2)==0)
			return  true;
		else
			return false;
	}
  //----------------------------------------------------------------
	// help all functions by getting the name of the writer
	function getWriter($str)
	{
		//$str='سامي الجابر على تويتر: "السعودية من اعلى الدول في نسبة المشاركة بشبكات التواصل الاجتماعي بمختلف قنوا';
		$pos= strpos($str, 'على تويتر');
		$writer = trim(substr($str,0,$pos));
		return $writer;
		
	}
   //----------------------------------------------------------------------------------
   //------------ gtw spammers---------------------------------------------------------
   //----------------------------------------------------------------------------------
	function gtw()
	{
		$theCountArray=array(); // count of date repeat array
		for ($i=0; $i<count($this->dates) ; $i++) // make date cout array
		{
			$counter=1;
			for ($j=0 ; $j<count($this->dates); $j++) 
			{
				if ($i!=$j)
				{
					if($this->matchStrings($this->dates[$i], $this->dates[$j]))
						$counter ++;
				}
			}
			$theCountArray[$i]=$counter;	
		}
		$counter=0; //make spamming writer array
		for ($i=0; $i < count($this->titles); $i++) // get the index of spammed
		{
			if ($theCountArray[$i] > 1)
			{
				$r[$counter]=$this->getWriter($this->titles[$i]);	
				$count++;
			}	
		}
		// remove duplicates
		$r= array_unique ($r, SORT_STRING);
		
		// rearrange index	
		foreach ($r as $a) 
			$this->spammers_gtw[] = trim($a);
		
	}
  
  //-----------------------------------------------------------------------------------------------
  //-------------gcs-------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------
	
	//--------- get similar percentage---------------------------------------------------------------
	function getSimPer($string1, $string2)
	{	//remove duplicates
		$string1 = implode(' ',array_unique(explode(' ', $string1)));
		$string2 = implode(' ',array_unique(explode(' ', $string2)));
		similar_text($string1, $string2, $percent); 
		return $percent;
	}

	//----------------gcs-----------------------------------------------------------------------------
	function gcs()
	{
		$this->spammers_gcs= null;
		$similarityArray= array();
		//set initail value of similarity to 0.0
		for ($i = 0 ; $i < count($this->contents) ; $i++)
			$similarityArray[$i]=0.0;

		for ($i = 0 ; $i < count($similarityArray) ; $i++)
		{
			for ($j = 0 ; $j < count($similarityArray) ; $j++)
			{
				if ($i <> $j)
				{
					$percentage =$this->getSimPer($this->contents[$i] , $this->contents[$j]);// get similar percentage
					if ($similarityArray[$i] < $percentage) 
						$similarityArray[$i] =$percentage;
					if ($similarityArray[$j] < $percentage) 
						$similarityArray[$j] =$percentage;
				}
			
			}
		}
		//printArray($similarityArray);
		//constant =75%
		$cv=75;
		$count=0;
		for ($i = 0 ; $i < count($similarityArray) ; $i++)
		{
			if ( $similarityArray[$i] > $cv)
			{
				$r[$count]= $this->getWriter($this->titles[$i]);
				$count++;
			}
		}
		// remove duplicates
		$r= array_unique ($r, SORT_STRING);
		
		// rearrange index	
		foreach ($r as $a) 
			$this->spammers_gcs[] = trim($a);
		
	}

  //--------------------------------------------------------------------------------------------
  //----------gedft-----------------------------------------------------------------------------
  //--------------------------------------------------------------------------------------------
	function gedft()
	{
		//get the earliest time
		$this->firstDate= date("Y-M-d");// get now date
		for ($i = 0 ; $i < count($this->dates) ; $i++)
		{
			if (strtotime($this->dates[$i]) < strtotime($this->firstDate))
				$this->firstDate= $this->dates[$i];
		}

		// get the date after 1 month
		$this->endDate = date("Y-M-d", strtotime("+1 monthS", strtotime($this->firstDate)));
		
		// return the posts that are within the first month
		$i=0;
		$count=0;
		foreach ($this->dates as $d)
		{
			if  (strtotime($d) < strtotime($this->endDate) ) 
			{	
				$r[$count]=  $this->getWriter($this->titles[$i]);
				$count++;
			}
			$i++;
		}
		// remove duplicates
		$r= array_unique ($r, SORT_STRING);
		
		// rearrange index	
		foreach ($r as $a) 
			$this->spammers_gedft[] = trim($a);		
	}

  // -------------------------------------------------------------------------------------------
  // -------------------ics---------------------------------------------------------------------
  // -------------------------------------------------------------------------------------------
	function ics()
	{
		$result=array_intersect($this->spammers_gtw, $this->spammers_gcs);
		$result=array_merge($result, array_intersect($this->spammers_gtw,$this->spammers_gedft));
		$result=array_merge($result, array_intersect($this->spammers_gcs,$this->spammers_gedft));
		$this->spammers_ics= array_unique($result);
	}

  // -------------------------------------------------------------------------------------------
  // -------------------ieft---------------------------------------------------------------------
  // -------------------------------------------------------------------------------------------
	function ieft()
	{		
		// merge the three arrays
		$al=null;
		$all= null;
		
		for ($i = 0; $i < count($this->spammers_gtw); $i++) 
			$al[] = $this->spammers_gtw[$i];
		for ($i = 0; $i < count($this->spammers_gcs); $i++) 
    			$al[] = $this->spammers_gcs[$i];
		for ($i = 0; $i < count($this->spammers_gedft); $i++) 
    			$al[] = $this->spammers_gedft[$i];
			
		// remove duplicates
		$al= array_unique ($al, SORT_STRING);
		
		// rearrange index	
		foreach ($al as $a) 
			$all[] = trim($a);
		//print_r($all);
		// check date
		for ($i=0 ; $i < count($this->titles) ; $i++)
		{
			for ($j = 0; $j < count($all); $j++)
			{
				if ( ($this->getWriter($this->titles[$i]) == $all[$j]) && (strtotime($this->dates[$i]) < strtotime($this->endDate))  ) 
				{					
					$r[]= $this->getWriter($this->titles[$i]);
				}
			}
		}
		// remove duplicates
		$r= array_unique ($r, SORT_STRING);
		
		// rearrange index	
		foreach ($r as $a) 
			$this->spammers_ieft[] = trim($a);
	}

  // -------------------------------------------------------------------------------------------
  // -------------------imc---------------------------------------------------------------------
  // -------------------------------------------------------------------------------------------
	function imc()
	{
		// merge the three arrays
		$al=null;
		$all= null;
		$r=null;
		for ($i = 0; $i < count($this->spammers_gtw); $i++) 
			$al[] = $this->spammers_gtw[$i];
		for ($i = 0; $i < count($this->spammers_gcs); $i++) 
    			$al[] = $this->spammers_gcs[$i];
		for ($i = 0; $i < count($this->spammers_gedft); $i++) 
    			$al[] = $this->spammers_gedft[$i];
			
		// remove duplicates
		$al= array_unique ($al, SORT_STRING);
		
		// rearrange index	
		foreach ($al as $a) 
			$all[] = trim($a);

		for ($i=0; $i<count($this->dates) ; $i++) // compare date and writers
		{
			for ($j=0 ; $j<count($this->dates); $j++) 
			{
				if ($i!=$j)
				{
					if($this->matchStrings($this->dates[$i], $this->dates[$j])) 
					{
						$writer= $this->getWriter($this->titles[$i]);
						foreach ($all as $a)
							if ($writer == $a)
								$r[] = $writer;
					}
				}
			}
				
		}
		
		// remove duplicates
		$r= array_unique ($r, SORT_STRING);
		
		// rearrange index	
		foreach ($r as $a) 
			$this->spammers_imc[] = trim($a);

	}

}// end of class
// ----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------


//utility functions
function printArray($theArray)
{
	foreach ($theArray as $value) 
		echo $value. "<br /><br />";
	
}


?>
<html dir="rtl" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
		<script src="uiLogic.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="frame">
			<img src="logo.jpg" alt="Lucene Search"/>
		</div>
		<br/>
		<div class="frame">
			<form method="post" name="Form" onsubmit="return validateForm()" action="index.php">
				<input type="text" placeholder="ادخل كلمة البحث" class="searchbox" id="search" name="searchText" />
				<button type="submit">
					بحث
				</button>
			</form>
		</div>
		<br/>
		<?php
		$tweets= new Tweets(); ?>
		<span>نتائج البحث</span><?= count($tweets->contents) ?>
		<div class="frame searchResults">
		<?php	
			echo '<h2> The tweets</h1>' ;	
			for ($i=0; $i<count($tweets->contents) ; $i++) 
			{			
				echo $tweets->getWriter($tweets->titles[$i]). '<br>' . $tweets->contents[$i] .
					 '<br>' .  $tweets->dates[$i]
					 .'<br><br>' ;
			}	
			$tweets->gtw();
			$tweets->gcs();
			$tweets->gedft();
			$tweets->ics();
			$tweets->ieft();
			$tweets->imc();

			if ($tweets->spammers_gtw<> null)
			{
				echo '<h2> نتائج GTW</h2>' ;	
				printArray($tweets->spammers_gtw);
			}
			else
				echo '<h2> لا يوجد  GTW Spammers</h2>' ;

			if ($tweets->spammers_gcs<> null)
			{
				echo '<h2> نتائج GCS</h2>' ;
				printArray($tweets->spammers_gcs);
			}
			else
				echo '<h2> لا يوجد  GCS Spammers</h2>' ;

			if ($tweets->spammers_gedft<> null)
			{
				echo '<h2> نتائج GEDFT</h2>' ;
				printArray($tweets->spammers_gedft);
			}
			else
				echo '<h2> لا يوجد  GEDFT Spammers</h2>' ;

			if ($tweets->spammers_ics<> null)
			{
				echo '<h2> نتائج ICS</h2>' ;
				printArray($tweets->spammers_ics);
			}
			else
				echo '<h2> لا يوجد  ICS Spammers</h2>' ;

			if ($tweets->spammers_ieft<> null)
			{
				echo '<h2> نتائج IEFT</h2>' ;
				printArray($tweets->spammers_ieft);
			}
			else
				echo '<h2> لا يوجد  IEFT Spammers</h2>' ;

			if ($tweets->spammers_imc<> null)
			{			
				echo '<h2> نتائج IMC</h2>' ;
				printArray($tweets->spammers_imc);
			}
			else
				echo '<h2> لا يوجد  IMC Spammers</h2>' ;



			echo '<br><br><br>';
		

?>
		</div>
	
</body>
</html>

