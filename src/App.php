<?php

namespace Shortener;

use Nette\Database\Connection;
use Nette\Database\Row;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class App{

    public function __construct(private Connection $db){

    }

    public function migrate(){
        $this->db->query(
            "DROP TABLE IF EXISTS `urls`;
            CREATE TABLE `urls` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `url` varchar(1024) DEFAULT '',
                `short_code` varchar(1024) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `last_accessed` timestamp,
                `hits` int NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;"
        );
    }

    // Simple shortcode generator
    public function simpleShortcode(){
        $short = substr(md5(uniqid(rand(), true)),0,8);
        
        //check if shortcode exists
        $short_exists = $this->db->query('SELECT short_code FROM urls WHERE short_code = ?', $short);
        $result = $short_exists->getRowCount();
        if($result > 0){
            //if exists re-run
            simpleShortcode();
        } else{
            return $short;
        }
    }

    public function handle(Request $request): Response{   
        $action = str_replace('/', '', $request->getPathInfo());

        switch ($action) {

            case "test":
                return $this->response("Test", 200);

            case "shorten":
                
                // Store a url in the database and return a shortened url...
                if(isset($_GET['url'])){
                    $long_url = $request->query->get('url');

                    $curl = curl_init($long_url);
                    curl_setopt($curl, CURLOPT_NOBODY, true); 
                    $curl_result = curl_exec($curl); 
  
                    if ($curl_result !== false) { 

                        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
                        // simple condition to check URL resolves
                        if ($statusCode < 400) { 

                            $short_url = $this->simpleShortcode();

                            //insert into db
                            $this->db->query('INSERT INTO urls', [
                                'url' => $long_url,
                                'short_code' => $short_url,
                            ]);

                            return $this->response("URL has been shortened to: <a href='http://" . $_SERVER['HTTP_HOST'] . "/" . $short_url . "'>" . $_SERVER['HTTP_HOST'] . "/" . $short_url . "</a>", 200);
                        } else { 
                            return $this->response("There was a problem finding this URL...."); 
                        } 
                    } else { 
                        return $this->response("There was a problem finding this URL...."); 
                    } 
                
                }else{
                    return $this->response("Are you using the correct params?", 418); 
                }

            case "stats":
                // Get stats about a given url and return a response...
                if(isset($_GET['short_code'])){

                    $stat_code = $request->query->get('short_code');
                    $code_exists = $this->db->fetchAll('SELECT * FROM urls WHERE short_code = ?', $stat_code);

                    if($code_exists){
                        return $this->response("Stats for short code: $stat_code <br/> Last Accessed: " . $code_exists[0]['last_accessed'] . "<br/> Hits: " . $code_exists[0]['hits'], 200); 
                    }else{
                        return $this->response("Cannot find shortcode, please try again"); 
                    }

                }else{  
                    return $this->response("Are you using the correct params?", 418); 
                }

            default:
                // Fetch a short url, update the hit counter, and return appropriate response (e.g. a Redirect or 404 Not Found)
                $requested_path = $request->getPathInfo();
                $requested_short = substr($requested_path, 1);

                if($requested_short != ""){

                    //check if short exists
                    $url_exists = $this->db->fetchAll('SELECT * FROM urls WHERE short_code = ?', $requested_short);

                    //if exists update last accessed and hit then redirect
                    if($url_exists){
                        
                        $this->db->query('UPDATE urls SET', [
                            'last_accessed' => date('Y-m-d H:i:s'),
                            'hits' => $url_exists[0]['hits'] + 1,
                        ], 'WHERE short_code = ?', $requested_short); 

                        if(strpos($url_exists[0]['url'], 'https://') !== false || strpos($url_exists[0]['url'], 'http://') !== false){
                            $response = new RedirectResponse($url_exists[0]['url'], 302);
                        }else{
                            $response = new RedirectResponse('https://' . $url_exists[0]['url'], 302);
                        }

                        return $this->response($response);

                    } else{
                        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
                        include("404.php");
                        die();
                    }

                }

        }

        return $this->response(content: 'URL Shortener Service');
    }

    private function response(string $content, int $status = 200, string $type =  'text/html'): Response{
        $response = new Response(
            'Content',
            $status,
            ['content-type' => $type]
        );

        $response->setContent($content);

        return $response->send();
    }
}
