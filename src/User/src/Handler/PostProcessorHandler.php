<?php

declare(strict_types=1);

namespace User\Handler;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\TableGateway\Feature\RowGatewayFeature;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
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

        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if (!empty($formData['id'])) {
            $postId = $formData['id'];
            $table = new TableGateway('scheduled_posts', $this->adapter, new RowGatewayFeature('id'));
            $post = $table->select(['id' => $postId])->current();
            $post->title = $formData['post_title'];
            $post->body = $formData['post_body'];
            $post->publish_on = $formData['post_publish_date'];
            $post->save();

            return new RedirectResponse('/' . $postId);
        }

        $rowGateway = new RowGateway('id', 'scheduled_posts', $this->adapter);
        $rowGateway->populate([
            'title' => $formData['post_title'],
            'body' => $formData['post_body'],
            'publish_on' => $formData['post_publish_date'],
            'user_id' => $session->get('user_id'),
        ]);
        $rowGateway->save();

        return new RedirectResponse('/' . $rowGateway->id);

    }
}
