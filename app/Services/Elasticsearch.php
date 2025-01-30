<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp\Exception\ClientException;

class Elasticsearch
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function createIndex($indexName, $mapping)
    {
        $response = $this->client->indices()->exists(['index' => $indexName]);
        if ($response->getStatusCode() === 404) {
            $this->client->indices()->create([
                'index' => $indexName,
                'body' => [
                    'mappings' => $mapping,
                ],
            ]);
        }
    }

    public function createDocument($indexName, $id, $body)
    {
        $params = [
            'index' => $indexName,
            'id' => $id,
            'body' => $body
        ];
        return $this->client->index($params);
    }

    public function updateDocument($indexName, $id, $data)
    {
        $this->client->update([
            'index' => $indexName,
            'id' => $id,
            'body' => [
                'doc' => $data,
            ],
        ]);
    }

    public function deleteDocument($indexName, $id)
    {
        $this->client->delete([
            'index' => $indexName,
            'id' => $id,
        ]);
    }

    public function search($indexName, $query)
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'type' => 'phrase_prefix',
                    ],
                ],
            ],
        ];
        return $this->client->search($params)['hits']['hits'];
    }

    function deleteIndex($indexName)
    {
        $params = [
            'index' => $indexName,
        ];
        try {
            $this->client->indices()->delete($params);
            printf("Index '%s' deleted\n", $indexName);
        } catch (ClientException $e) {
            printf("Error deleting index '%s': %s\n", $indexName, $e->getMessage());
        }
    }
}
