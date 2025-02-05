<?php
namespace Core\Utils;

use Predis\Client;

class Redis
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
    }

    /**
     * Obtém o cliente Redis.
     *
     * @return Client Cliente Redis.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Verifica se uma chave existe no cache.
     *
     * @param string $key Chave do cache.
     * @return bool Verdadeiro se a chave existe, falso caso contrário.
     */
    public function exists($key)
    {
        return $this->client->exists($key);
    }

    /**
     * Obtém o valor de uma chave no cache.
     *
     * @param string $key Chave do cache.
     * @return string Valor armazenado na chave.
     */
    public function get($key)
    {
        return $this->client->get($key);
    }

    /**
     * Armazena um valor no cache com uma chave específica e um tempo de expiração.
     *
     * @param string $key Chave do cache.
     * @param string $value Valor a ser armazenado.
     * @param int $ttl Tempo de expiração em segundos.
     * @return void
     */
    public function set($key, $value, $ttl = 3600)
    {
        $this->client->setex($key, $ttl, $value);
    }

    /**
     * Remove uma chave do cache.
     *
     * @param string $key Chave do cache.
     * @return void
     */
    public function del($key)
    {
        $this->client->del([$key]);
    }
}
