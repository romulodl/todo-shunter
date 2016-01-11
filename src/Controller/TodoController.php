<?php
namespace Todo\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController
{
    private $item = null;

    public function __construct($item = null)
    {
        $this->item = $item ?: \Model::factory('Todo\Model\Item');
    }

    public function indexAction(Request $request, Application $app)
    {
        $return = [];
        $items  = $this->item->order_by_desc('id')
            ->find_many();

        foreach ($items as $i => $item) {
            $return[$i]['id']    = (int) $item->id;
            $return[$i]['title'] = $item->title;
            $return[$i]['done']  = (bool) $item->done;
        }

        return new Response(
            json_encode(['items' => $return]),
            200,
            ['Content-Type' => $app['content-type']]
        );
    }

    public function singleAction(Request $request, Application $app, $id)
    {
        if (!$item = $this->item->find_one($id)) return $this->notFound();

        return new Response(
            json_encode(
                [
                    'item' => [
                        'id'    => (int) $item->id,
                        'title' => $item->title,
                        'done'  => (bool) $item->done,
                    ]
                ]
            ),
            200,
            ['Content-Type' => $app['content-type']]
        );
    }

    public function addAction(Request $request, Application $app)
    {
        $return = [
            'message' => 'ERROR!',
            'error'   => true
        ];

        if ($title = $request->request->get('title')) {
            $item = $this->item->create();
            $item->title = $title;
            $item->done = false;
            if ($item->save()) {
                $return = [
                    'message' => 'Item added successfully',
                    'error'   => false
                ];
            }
        }

        return new Response(json_encode($return), 200, ['Content-Type' => $app['content-type']]);
    }

    public function editAction(Request $request, Application $app, $id)
    {
        if (!$item = $this->item->find_one($id)) return $this->notFound();

        $title = $request->request->get('title');
        if ($title !== null || $title === '') {
            $item->title = $title;
        }
        $item->done = $request->request->get('done') === 'true';

        $return = [
            'message' => 'ERROR!',
            'error'   => true
        ];

        if ($item->save()) {
            $return = [
                'message' => "Item {$id} updated successfully",
                'error'   => false
            ];
        }

        return new Response(json_encode($return), 200, ['Content-Type' => $app['content-type']]);
    }

    private function notFound()
    {
        return new Response(
            json_encode(
                [
                    'error'   => true,
                    'message' => 'Item not found'
                ]
            ),
            404,
            $app['content-type']
        );
    }
}
