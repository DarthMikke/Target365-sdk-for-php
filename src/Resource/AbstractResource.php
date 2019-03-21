<?php

declare(strict_types = 1);

namespace Target365\ApiSdk\Resource;

use Psr\Http\Message\ResponseInterface;
use Target365\ApiSdk\ApiClient;
use Target365\ApiSdk\Exception\ApiClientException;
use Target365\ApiSdk\Model\AbstractModel;

abstract class AbstractResource
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    abstract protected function getResourceUri(): string;

    abstract protected function getResourceModelFqns(): string;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Confirms that the model passed is of a specific subclass which corresponds to this resource.
     *
     * @param AbstractModel $model
     */
    protected function forceResourceModel(AbstractModel $model): void
    {
        if (!is_a($model, $this->getResourceModelFqns())) {
            throw new ApiClientException('You must pass instance of ' . $this->getResourceModelFqns());
        }
    }

    protected function decodeResponseJson(ResponseInterface $response): array
    {
        return \GuzzleHttp\json_decode($response->getBody()->__toString(), true);
    }

    protected function instantiateModel(array $data): AbstractModel
    {
        $fqns = $this->getResourceModelFqns();
        $model = new $fqns();
        $model->populate($data);
        return $model;
    }

    protected function parseIdentifierFromLocationHeader(string $locationHeader)
    {
        // https://test.target365.io/api/keywords/111
        $chunks = explode('/', $locationHeader);
        $identifier = end($chunks);
        return $identifier;
    }
}
