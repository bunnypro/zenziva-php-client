<?php namespace Bunnypro\Zenziva;

use Bunnypro\Zenziva\Exceptions\PassNotDefinedException;
use Bunnypro\Zenziva\Exceptions\ServiceNotDefined;
use Bunnypro\Zenziva\Exceptions\UserNotDefinedException;
use GuzzleHttp\Client as GuzzleHttpClient;

class Client
{
    const URI = 'http://%s.zenziva.net/apps/%s.php';

    const SERVICES = [
        'reguler' => [
            'name' => 'reguler',
            'action' => 'smsapi',
        ],
        'masking' => [
            'name' => 'alpha',
            'action' => 'smsapi',
        ],
        'center' => [
            'name' => 'demo',
            'action' => 'sendsms',
        ]
    ];

    protected $user;

    protected $pass;

    protected $service = 'reguler';

    protected $type = 'reguler';

    public function __construct(array $config = [])
    {
        $this->user = (array_key_exists('user', $config) ? $config['user'] : null);

        $this->pass = (array_key_exists('pass', $config) ? $config['pass'] : null);

        $this->service = (array_key_exists('service', $config) ? $config['service'] : $this->service);
    }

    public static function create(array $config = [])
    {
        return new static($config);
    }

    public function service($service)
    {
        $this->service = $service;

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function send($to, $message = '')
    {
        $this->assertCanSend();

        return (new GuzzleHttpClient())->request('GET', $this->buildUrl(), [
            'query' => $this->buildQuery([
                'nohp' => $to,
                'pesan' => $message,
            ])
        ])->getBody();
    }

    protected function assertCanSend()
    {
        if (! array_key_exists($this->service, self::SERVICES)) {
            throw new \Exception('zenziva service is not match: ' . $this->service);
        }
    }

    protected function buildUrl()
    {
        $service = self::SERVICES[$this->service];

        return sprintf(self::URI, $service['name'], $service['action']);
    }

    protected function buildQuery($merged = [])
    {
        $query = [
            'userkey' => $this->user,
            'passkey' => $this->pass,
        ];

        if ($this->service == 'center') {
            $query['type'] = $this->type;
        }

        return array_merge($merged, $query);
    }
}