# Group-Spammers-Detection
This project deal with Arabic text which is written in the social media. 
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
