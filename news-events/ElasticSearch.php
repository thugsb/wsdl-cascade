<?php

include('_events_array.html');

/*
Useful docs
v 2.3
https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html

*/

function putEvents($events) {
    foreach ($events as $key => $event) {
        $json = json_encode($event);
        $command = "curl -XPUT 'http://localhost:9200/slc/event/".$event['id']."' -d '$json'";
        echo '<script>console.log(';
        echo exec($command);
        echo ');</script>';
    }
}
function showEvents($events) {
    foreach ($events as $key => $event) {
        $json = json_encode($event, JSON_PRETTY_PRINT);
        echo '<pre>';
        echo $json;
        echo '</pre>';
    }
}

// putEvents($events);
// showEvents($events);

$now = time()*1000;



$searchQuery = '{
    "query" : {
        "bool" : {
            "filter" : {
                "range" : {
                    "start" : { "gt" : '.$now.' } 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Filter:Future.");</script>';


$searchQuery = '{
    "query" : {
        "bool" : {
            "filter" : {
                "range" : {
                    "start" : { "gt" : '.$now.' },
                    "start" : { "lte" : '.($now+604800000).' }
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Filter:The next 7 days.");</script>';



$searchQuery = '{
    "query" : {
        "bool" : {
            "must" : {
                "match" : {
                    "audiences" : "Graduate" 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Audience:Graduate match.");</script>';


$searchQuery = '{
    "query" : {
        "bool" : {
            "must" : {
                "multi_match" : {
                    "query":    "reading", 
                    "fields": [ "title", "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Simple Multimatch.");</script>';


$searchQuery = '{
    "query" : {
        "bool" : {
            "should": [
                {
                    "term" : {
                        "title":    "reading",
                        "boost" : 2.0
                    }
                },
                {
                    "multi_match" : {
                        "query":    "reading", 
                        "fields": [ "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                    }
                }
            ]
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Title-prioritized Multimatch.");</script>';


$searchQuery = '{
    "sort" : {
        "start" : { "order" : "asc" }
    },
    "query" : {
        "bool" : {
            "must" : {
                "multi_match" : {
                    "query":    "reading", 
                    "fields": [ "title", "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Simple Multimatch, sorted by date.");</script>';


$searchQuery = '{
    "query" : {
        "bool" : {
            "must" : {
                "multi_match" : {
                    "query":    "qxqxqx", 
                    "fields": [ "title", "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Matching:qxqxqx.");</script>';


$searchQuery = '{
    "query" : {
        "bool" : {
            "must" : {
                "multi_match" : {
                    "query":    "mfa writing", 
                    "fields": [ "title", "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                }
            },
            "filter" : {
                "range" : {
                    "start" : { "gt" : '.$now.' } 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Future-filtered Multimatch.");</script>';


$searchQuery = '{
    "query" : {
        "bool" : {
            "must" : {
                "multi_match" : {
                    "query":    "mfa writing", 
                    "fields": [ "title", "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                }
            },
            "filter" : {
                "match" : {
                    "audiences" : "Graduate" 
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Audience:Graduate-filtered Multimatch.");</script>';



$searchQuery = '{
    "query" : {
        "bool" : {
            "must" : {
                "multi_match" : {
                    "query":    "mfa writing", 
                    "fields": [ "title", "content", "location", "audiences", "academics", "faculty", "sponsors", "themes" ] 
                }
            },
            "filter" : {
                "bool" : {
                    "must" : [
                        {
                            "range" : {
                                "start" : { "gt" : '.$now.' } 
                            }
                        },
                        {
                            "term" : {
                                "audiences" : "Graduate" 
                            }
                        }
                    ]
                }
            }
        }
    }
}';
$command = "curl -XGET 'http://localhost:9200/slc/event/_search' -d '$searchQuery'";
echo '<script>results = ';
echo exec($command);
echo '; console.log(results); console.log(results.hits.total+ " results from Future-filtered, Audience:Graduate-filtered Multimatch.");</script>';




?>