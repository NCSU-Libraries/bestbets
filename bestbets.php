<?php
// Cory Lown, NCSU Libraries, March 2012
// Class for setting up best bet request to solr.
// Functions for getting solr response data.


class BestBet {
    
    public function __construct() {
        $this->query = '';
        $this->id = '';
        $this->title = '';
        $this->url = '';
        $this->description = '';
        $this->keywords = array();
        $this->errors = array();
    }
    
    function solrQuery($query) {
        
        // store the query
        $this->query = $query;
        
        // define some variables
        $limit = 1;
        $params = array();
        $bestbets_query = str_replace('"','',$this->query);
        $results = '';
        $errors = array();
        
        // modifies query behavior for shorter
        // and longer queries
        if ($bestbets_query == '*:*') {
            // for getting all documents in solr index
            $bestbets_query = "*:*";
            $limit = 100;
        } elseif (substr_count($bestbets_query, ' ') < 3) {
            // for queries containing 3 or fewer terms
            // use non-analyzed keyword field with phrased search
            $bestbets_query = 'keywords: "' . $bestbets_query . '"';
        } else {
        // use DisMax query parser for longer queries
        // NOTE: edismax parser causes too liberal matching if the word "or"
        //       is present in the query
        // force at least 4 terms to match
            $params = array('defType' => 'dismax', 'mm' => '4');
        }
        
        // instantiate new SolrPhpClient service with connection
        // to best bets solr index
        $solr = new Apache_Solr_Service('host', port, 'solrpath');
        
        if (get_magic_quotes_gpc() == 1) {
            $bestbets_query = stripslashes($bestbets_query);
        }
        
        // make solr request and store response
        
        try {
            $results = $solr->search($bestbets_query, 0, $limit, $params);
        } catch ( Exception $e ) {
            $errors[] = $e->getMessage();
        }
        
        $this->errors = $errors;
        
        // loop through response to extract documents
        // store in an array
        if ($results->response->docs) {
            foreach ($results->response->docs as $doc) {
                $fields = array();
                foreach ($doc as $field => $value) {
                    $fields[$field] = $value;
                }
                $response[] = $fields;
            }
        }
        // full response
        $this->results = $results;
        
        // all results
        $this->docs = $response;
        
        // id, title, url, description, keywords of first result
        $this->id = $response[0]['id'];
        $this->title = $response[0]['title'];
        $this->url = $response[0]['url'];
        $this->description = $response[0]['description'];
        $this->keywords = $response[0]['keywords'];
    }

    // Getters for query result values
    function getQuery() {
        return $this->query;
    }
        
    function getDocs() {
        return $this->docs;
    }
    
    function getNumFound() {
        return $this->results->response->numFound;
    }
    
    function getId() {
        return $this->id;
    }

    function getTitle() {
        return $this->title;
    }

    function getUrl() {
        return $this->url;
    }

    function getDescription() {
        return $this->description;
    }
    
    function getKeywords() {
        return $this->keywords;
    }
    
    function getErrors() {
        return $this->errors;
    }
}