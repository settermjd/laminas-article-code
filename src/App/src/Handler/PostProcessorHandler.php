<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\TableGateway\Feature\RowGatewayFeature;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class PostProcessorHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $renderer;
    private Adapter $adapter;

    public function __construct(TemplateRendererInterface $renderer, Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $formData = $request->getParsedBody();

        if ($formData === null) {
            return new RedirectResponse('/');
        }

        $table = new TableGateway('scheduled_posts', $this->adapter, new RowGatewayFeature('id'));

        if (!empty($formData['id'])) {
            $scheduledPosts = $table->select(['id' => $formData['id']]);
            $post = $scheduledPosts->current();
            $post->title = $formData['post_title'];
            $post->body = $formData['post_body'];
            $post->publish_on = $formData['post_publish_date'];
            $post->save();
            return new RedirectResponse('/' . $post->id);
        }

        $table->insert([
            'title' => $formData['post_title'],
            'body' => $formData['post_body'],
            'publish_on' => $formData['post_publish_date']
        ]);
        $sql    = new Sql($this->adapter);
        $select = $sql->select('scheduled_posts');
        $select->columns(['id' => new Expression('MAX(id)')]);
        $selectString = $sql->buildSqlString($select);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);

        return new RedirectResponse('/' . $results->current()->id);

    }
}
