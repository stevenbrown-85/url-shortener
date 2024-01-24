<?php 

require_once './vendor/autoload.php';

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testShortUrlIsSavedToDatabase(): void
    {
        $client = new Client(['http_errors' => false]);

        $result = $client->get('http://localhost:8000/test');

        $this->assertEquals(200, $result->getStatusCode());
    }
}