# Group-Spammers-Detection
This project deal with Arabic text which is written in the social media. 
Group spamming detection is of two main categories. The first category is a group of spammers who works together to promote a target entity or to ruin the reputation of another. Here the individuals in the group may or may not know each other. The second category is a single person who registers with multiple identities and post spams using these multiple identities. This is called sock puppetting
This type of social media spamming is the most dengerous type of spamming
It has four main steps which are: crawling, preprocessing, spamming activities detection,  and Individual members’ behaviors scanning.
It is considered a search engine It uses Nutch web crawler. 
It is integrated with Solr to provide indexing and searching capabilities to enhance the performance. 
Nutch and Solr can handle big data effectively. 
Nutch crawled the twitter website and stored and indexed the details of some web pages from twitter website. 
The index is created using Solr, which is actually Lucene index. 
Important information about each URL are stored such as the title, the URL, the content of the page, the anchors to other pages, metatag description and metatag keywords are stored. 
The data are ready to be searched using Solr. 
Using PHP the algorithm of groups of spammers detection is applied 
You  need to install Nutch, Solr and PHP to make work this project work
Algorithm:
3.4.3.1	GTW (Group Time Window)
This rule detects if the group member posted reviews on a particular entity during a short time interval which is considered a spamming activity(Mukherjee et al, 2012). The following pseudocode can determine the existence of this rule:
 

Get the tweets for the searched entity
For each day 
If the number of tweets > constant value
The tweets of that day are suspected; (Mukherjee et al, 2012)


I presume that the constant value is 5. Then, if there are 5 tweets or more on the searched entity which are posted in the same day, these tweets of that day are suspected tweets. Consequently, the writers of them are suspected spammers of this entity. 

3.4.3.2	GCS (Group Content Similarity)
Content similarity can be duplicate or near duplicate tweets (Mukherjee et al, 2012). These tweets are suspected spamming activity which can be detected using the following pseudocode. 
For ( i = 0 ; i < count (tweet) ; i++)
{
For ( j = i+1 ; j < count (tweet) ; j++)
{
String A = tweet [i].string;
String B = tweet [j].string;
Int tweet[i].c =Compare (string A , String B)// many similar words between the2 string
Float tweet[i]. tweet percentage = teweet[i].c / total number of words in tweet[i] 
If (tweet [i].similarity < tweet[i].percentage)
 tweet [i].similarity = tweet[i]. percentage;
If (tweet [j].similarity < tweet[j].percentage) 
tweet [j].similarity = tweet[j]. percentage;
}
}
Constant value = 75%
For ( i = 0 ; i < count (tweet) ; i++)
{
If (tweet [i]. similarity > constant value)
Return tweet[i].writer;
} (Mukherjee et al, 2012)

The similarity of the content is to be checked for each tweet and the other tweets. If the similarity of this tweet and another one is 75% or more. Then, these tweets are suspected spam and their writers are suspected spammers. 

3.4.3.3	GETF (Group Early Time Frame)
This condition detects if a group members are first people to review an entity. The exact date of launching each entity is not available, consequently, the first review of this entity is considered the launching date of it (Mukherjee et al, 2012).
a= get Earliest tweet date 
For (int i=0 ; i<tweet.count ; i++)
If tweet.date > a + 6 month
It's a good tweet	
	Else
	   It's a suspected tweet (Mukherjee et al, 2012).
I presume that the first six month is the period of the early tweets (Mukherjee et al, 2012). The collected tweets from all of the previous conditions are to be checked in the following stage. 

3.4.4	Individual Members’ Behaviors Scanning
In this stage, individual member behavior is to be monitored to extract the suspected spammers (Mukherjee et al, 2012). The tweets' writers of the previous stage are the only ones to be checked here. The features are explained below:
3.4.4.1	ICS (Individual Content Similarity)
Individual spammers may review a product many times posting duplicate or near duplicate reviews to increase the product popularity. It models the activity of a particular reviewer to examine all his reviews towards a product to see if there are duplicated reviews on this particular entity (Mukherjee et al, 2012). The pseudocode explains the steps

Get the id of the members who wrote tweets about this product
If the id is repeated and the content is duplicate or near duplicate
			The member is a suspected spammer (Mukherjee et al, 2012)


3.4.4.2	IMC (Individual Member Coupling in a Group)
IMC detects if members worked with other members of the group by posting at the same date about the same product. This is considered tightly coupled members which indicate group spamming. This needs to find the difference between the posting date of member for the defined product and the average posting date members of the group for this particular product (Mukherjee et al, 2012).
Get the tweets of product p
Get the average time between the posts
For each tweet
	Get the difference between the posting date of every member and the average posting date 
	If the difference is < 5 
		The members who has this difference is considered spammers.
(Mukherjee et al, 2012)
I presumed that 5 is the value to indicate the difference from the average. Exceeding this value means this member is not coupled with others. 
