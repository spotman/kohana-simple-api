<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Model\UserInterface;
use DateTime;
use DateTimeImmutable;
use Invoker\InvokerInterface;
use JsonSerializable;
use Traversable;

final class ApiMethodResponseConverter implements ApiMethodResponseConverterInterface
{

    private static $allowedResultTypes = [
        'null',
        'boolean',
        'string',
        'integer',
        'double',
    ];

    /**
     * @var \Invoker\InvokerInterface
     */
    private $invoker;

    /**
     * ApiMethodResponseConverter constructor.
     *
     * @param \Invoker\InvokerInterface $invoker
     */
    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * @param \Spotman\Api\ApiMethodResponse  $response
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiMethodException
     */
    public function convert(ApiMethodResponse $response, UserInterface $user): ApiMethodResponse
    {
        $data         = $response->getData();
        $lastModified = (new DateTime)->setTimestamp($response->getLastModified()->getTimestamp());

        $data         = $this->convertResult($data, $lastModified, $user);
        $lastModified = (new DateTimeImmutable())->setTimestamp($lastModified->getTimestamp());

        return new ApiMethodResponse($data, $lastModified);
    }

    /**
     * @param mixed                           $modelCallResult
     * @param \DateTime                       $lastModified
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return array|int|string|bool|double|null
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResult($modelCallResult, DateTime $lastModified, UserInterface $user)
    {
        if (\is_object($modelCallResult)) {
            return $this->convertResultObject($modelCallResult, $lastModified, $user);
        }

        if (\is_array($modelCallResult)) {
            return $this->convertResultTraversable($modelCallResult, $lastModified, $user);
        }

        return $this->convertResultSimple($modelCallResult);
    }

    /**
     * @param                                 $object
     * @param \DateTime                       $lastModified
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return int|string|array|mixed
     * @throws \Invoker\Exception\InvocationException
     * @throws \Invoker\Exception\NotCallableException
     * @throws \Invoker\Exception\NotEnoughParametersException
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultObject($object, DateTime $lastModified, UserInterface $user)
    {
        if ($object instanceof ApiResponseItemInterface) {
            // Get item`s last modified time for setting it in current response
            $lm = $object->getApiLastModified();

            if ($lm && $lm > $lastModified) {
                $lastModified->setTimestamp($lm->getTimestamp());
            }

            $handler = $object->getApiResponseData();

            if (!is_callable($handler)) {
                throw new ApiMethodException('Response must be callable');
            }

            $response = $this->invoker->call($handler, ['user' => $user]);

            return $this->convertResult($response, $lastModified, $user);
        }

        if ($object instanceof JsonSerializable) {
            return $object->jsonSerialize();
        }

        if ($object instanceof Traversable) {
            return $this->convertResultTraversable($object, $lastModified, $user);
        }

        throw new ApiMethodException(
            'API model method may return objects implementing Traversable or ApiModelResponseItemInterface only'
        );
    }

    /**
     * @param array|\Traversable              $traversable
     * @param \DateTime                       $lastModified
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return array
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultTraversable($traversable, DateTime $lastModified, UserInterface $user): array
    {
        $data = [];

        foreach ($traversable as $key => $value) {
            $data[$key] = $this->convertResult($value, $lastModified, $user);
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @return mixed
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultSimple($data)
    {
        $type = \gettype($data);

        if (!\in_array(strtolower($type), static::$allowedResultTypes, true)) {
            throw new ApiMethodException('API model must not return values of type :type', [':type' => $type]);
        }

        return $data;
    }
}
