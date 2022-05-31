<?php
declare(strict_types=1);

namespace User\Command\LinkedIn;

use User\Database\ScheduledPostsTableGateway;
use GuzzleHttp\Client;
use Laminas\Db\TableGateway\TableGatewayInterface;
use League\OAuth2\Client\Provider\LinkedIn;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishToLinkedInCommand extends Command
{
    private LinkedIn $client;

    /** @var ScheduledPostsTableGateway $table */
    private TableGatewayInterface $table;

    /** @var string */
    protected static $defaultName = 'publish-post';

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
    }

    public function __construct(LinkedIn $client, TableGatewayInterface $table)
    {
        parent::__construct();

        $this->client = $client;
        $this->table = $table;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $posts = $this->table->getAllPosts();
        $output->writeln(sprintf('%d available posts found', $posts->count()));
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://www.linkedin.com',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        $response = $client->request('POST', '/oauth/v2/accessToken', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $_SERVER['LINKEDIN_CLIENT_ID'],
                'client_secret' => $_SERVER['LINKEDIN_CLIENT_SECRET']
            ]
        ]);

        var_dump($response->getBody());
        exit();

        return 0;
    }
}