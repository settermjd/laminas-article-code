<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\Feature\RowGatewayFeature;
use Laminas\Db\TableGateway\TableGateway;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class UserProfileHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $renderer;
    private UsersTableGateway $table;

    public function __construct(TemplateRendererInterface $renderer, UsersTableGateway $table)
    {
        $this->renderer = $renderer;
        $this->table = $table;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $user = $this->table->select(['id' => $session->get('user_id')])->current();

        return new HtmlResponse($this->renderer->render(
            'app::user-profile',
            [
                'user' => $user,
            ]
        ));
    }
}
