<?php

namespace Shortener;

use Nette\Database\Connection;
use Nette\Database\Row;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class App
{
    public function __construct(private Connection $db)
    {
    }

    public function migrate()
    {
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

    public function handle(Request $request): Response
    {   
        $action = str_replace('/', '', $request->getPathInfo());

        switch ($action) {

            case "test":
                return $this->response("Test", 200);

            case "shorten":
                // Store a url in the database and return a shortened url...

            case "stats":
                // Get stats about a given url and return a response...

            default:
                // Fetch a short url, update the hit counter, and return appropriate response (e.g. a Redirect)
        }

        return $this->response(content: 'Default message...');
    }

    private function response(string $content, int $status = 200, string $type =  'text/html'): Response
    {
        $response = new Response(
            'Content',
            $status,
            ['content-type' => $type]
        );

        $response->setContent($content);

        return $response->send();
    }
}
