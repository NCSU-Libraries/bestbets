BestBets
===========

BestBets is a system designed to provide accurate, prominent results to the most common queries on your website or search engine. Although relevance ranking works well most of the time, sometimes common known item searches fail because algorithms cannot be optimized for all cases. BestBets lets you fine tune retrieval for a limited number of common searches so your best result appears first. See Louis Rosenfeld's Search Analytics for Your Site|http://rosenfeldmedia.com/books/search-analytics/ about the benfits of this method.

Terms Of Use
===========
    
MIT/X11 License
See included LICENSE.txt or http://www.opensource.org/licenses/mit-license.php


Installation
===========

Drop the contents of the git repository in a web accessible directory on your server.

Install Solr-PhpClient from https://code.google.com/p/solr-php-client/

Install Solr from http://lucene.apache.org/solr/. (BestBets is known to work with Solr 3.5.)

Start a Solr core using the schema.xml in this repository.

There are two places in the code where you'll need to specify where your running Solr core can be accessed, once in bestbets.php and once in updateBestBetsIndex.sh.

Once you index the data in bestbets.xml  with the shell script you can use the BestBet class like so:

<?php
require_once('SolrPhpClient/Service.php');
require_once('bestbets.php');
$bestbet = new BestBet();
$bestbet->solrQuery('hours');
echo $bestbet->getTitle;
echo $bestbet->getUrl;
echo $bestbet->getDescription;
?>

Note
============

Short and long queries are handled differently. If the query has three or fewer terms it forces an exact match on the un-tokenized keyword field. For longer queries we use the dismax parser on a tokenized copy of the keyword field and set the min match to 4. This seems to help prevent too many false positives.