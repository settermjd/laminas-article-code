<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $template;
    private Adapter $adapter;

    public function __construct(TemplateRendererInterface $template, Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];
        $data['scheduled_posts'] = $this->getScheduledPosts();

        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $postId = $request->getAttribute('id');
        if ($postId !== null) {
            $scheduledPosts = new TableGateway('scheduled_posts', $this->adapter);
            $row = $scheduledPosts->select(['id' => $postId])->current();
            if ($row !== null) {
                $data['current_record'] = $row;
            }
        }

        return new HtmlResponse($this->template->render('app::home-page', $data));
    }

    public function getScheduledPosts(): array
    {
        $data = [];
        $sql = new Sql($this->adapter);
        $select = $sql
            ->select('scheduled_posts')
            ->order('publish_on DESC');
        $selectString = $sql->buildSqlString($select);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);

        foreach ($results as $result) {
            $publishDate = (new \DateTimeImmutable($results->current()->publish_on));
            $data[$publishDate->format('Y.m.d')][] = $result;
        }

        return $data;
    }
}
