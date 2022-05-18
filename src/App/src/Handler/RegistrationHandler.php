<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\RowGateway\RowGateway;
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

class RegistrationHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $renderer;
    private Adapter $adapter;

    public function __construct(TemplateRendererInterface $renderer, Adapter $adapter)
    {
        $this->renderer = $renderer;
        $this->adapter = $adapter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($request->getMethod() === 'GET') {
            return new HtmlResponse($this->renderer->render(
                'app::registration',
                []
            ));
        }

        $formData = $request->getParsedBody();
        $rowGateway = new RowGateway('id', 'users', $this->adapter);
        $rowGateway->populate([
            'email_address' => $formData['email_address'],
            'password' => $formData['password'],
            'first_name' => $formData['first_name'],
            'last_name' => $formData['last_name'],
        ]);
        $rowGateway->save();

        $table = new TableGateway('users', $this->adapter, new RowGatewayFeature('id'));
        $user = $table->select(['email_address' => $formData['email_address']])->current();

        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->set('user_id', $user->id);

        return new RedirectResponse('/');
    }
}
