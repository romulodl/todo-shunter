<?php

use Silex\WebTestCase;

class TodoTest extends WebTestCase
{
    public function createApplication()
    {
        $env = 'test';
        $app = require __DIR__ . '/../index.php';
        unset($app['exception_handler']);

        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        exec(__DIR__ . '/../vendor/bin/phinx migrate -e testing');

        $item = Model::factory('Todo\Model\Item');
        $item->delete_many();
        ORM::for_table('SQLITE_SEQUENCE')->raw_execute("delete from SQLITE_SEQUENCE where name='item';");

        $item->create();
        $item->title = 'Test title';
        $item->done  = false;
        $item->save();
    }

    public function testHomePage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertSame(
            '{"items":[{"id":1,"title":"Test title","done":false}]}',
            $client->getResponse()->getContent()
        );
    }

    public function testSinglePage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/item/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertSame(
            '{"item":{"id":1,"title":"Test title","done":false}}',
            $client->getResponse()->getContent()
        );
    }

    public function testAddPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/add', ['title' => 'New Test']);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertSame(
            '{"message":"Item added successfully","error":false}',
            $client->getResponse()->getContent()
        );
    }

    public function testEditPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/edit/1', [
            'title' => 'New new test',
            'done'  => 'true'
        ]);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertSame(
            '{"message":"Item 1 updated successfully","error":false}',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/item/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertSame(
            '{"item":{"id":1,"title":"New new test","done":true}}',
            $client->getResponse()->getContent()
        );
    }
}
