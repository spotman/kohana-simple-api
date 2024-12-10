<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Exception;
use BetaKiller\Model\LanguageInterface;
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
     * @param \Spotman\Api\ApiMethodInterface     $method
     * @param \Spotman\Api\ApiMethodResponse      $response
     * @param \BetaKiller\Model\UserInterface     $user
     * @param \BetaKiller\Model\LanguageInterface $lang
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiMethodException
     */
    public function convert(
        ApiMethodInterface $method,
        ApiMethodResponse $response,
        UserInterface $user,
        LanguageInterface $lang
    ): ApiMethodResponse {
        $data         = $response->getData();
        $lastModified = (new DateTime)->setTimestamp($response->getLastModified()->getTimestamp());

        $data         = $this->convertResult($method, $data, $lastModified, $user, $lang);
        $lastModified = (new DateTimeImmutable())->setTimestamp($lastModified->getTimestamp());

        return new ApiMethodResponse($data, $lastModified);
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface     $method
     * @param mixed                               $modelCallResult
     * @param \DateTime                           $lastModified
     * @param \BetaKiller\Model\UserInterface     $user
     * @param \BetaKiller\Model\LanguageInterface $lang
     *
     * @return array|int|string|bool|double|null
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResult(
        ApiMethodInterface $method,
        $modelCallResult,
        DateTime $lastModified,
        UserInterface $user,
        LanguageInterface $lang
    ) {
        if ($modelCallResult === null || \is_scalar($modelCallResult)) {
            return $this->convertResultSimple($modelCallResult);
        }

        if (is_callable($modelCallResult)) {
            return $this->convertResultCallable($method, $modelCallResult, $lastModified, $user, $lang);
        }

        if (\is_object($modelCallResult)) {
            $startedAt = microtime(true);
            $result = $this->convertResultObject($method, $modelCallResult, $lastModified, $user, $lang);
            $executedIn = (microtime(true) - $startedAt) * 1000;

            if (is_array($result) && is_string(array_key_first($result)) && $user->isDeveloper()) {
                $result += [
                    '__perf__' => round($executedIn, 1),
                ];
            }

            return $result;
        }

        if (\is_array($modelCallResult)) {
            return $this->convertResultTraversable($method, $modelCallResult, $lastModified, $user, $lang);
        }

        throw new Exception('Unknown API response data type :what', [
            ':what' => \gettype($modelCallResult),
        ]);
    }

    private function convertResultCallable(
        ApiMethodInterface $method,
        callable $handler,
        DateTime $lastModified,
        UserInterface $user,
        LanguageInterface $lang
    ) {
        $response = $this->invoker->call($handler, [
            'method' => $method,
            'user'   => $user,
            'lang'   => $lang,
        ]);

        return $this->convertResult($method, $response, $lastModified, $user, $lang);
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface     $method
     * @param                                     $object
     * @param \DateTime                           $lastModified
     * @param \BetaKiller\Model\UserInterface     $user
     * @param \BetaKiller\Model\LanguageInterface $lang
     *
     * @return int|string|array|mixed
     * @throws \Invoker\Exception\InvocationException
     * @throws \Invoker\Exception\NotCallableException
     * @throws \Invoker\Exception\NotEnoughParametersException
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultObject(
        ApiMethodInterface $method,
        $object,
        DateTime $lastModified,
        UserInterface $user,
        LanguageInterface $lang
    ) {
        if ($object instanceof ApiResponseItemInterface) {
            // Get item`s last modified time for setting it in current response
            $lm = $object->getApiLastModified();

            if ($lm && $lm > $lastModified) {
                $lastModified->setTimestamp($lm->getTimestamp());
            }

            $handler = $object->getApiResponseData();

            if (!is_callable($handler)) {
                throw new ApiMethodException('Response must be callable but :type given', [
                    ':type' => gettype($handler),
                ]);
            }

            return $this->convertResultCallable($method, $handler, $lastModified, $user, $lang);
        }

        if ($object instanceof JsonSerializable) {
            return $this->convertResult($method, $object->jsonSerialize(), $lastModified, $user, $lang);
        }

        if ($object instanceof Traversable) {
            return $this->convertResultTraversable($method, $object, $lastModified, $user, $lang);
        }

        throw new ApiMethodException(
            'API may return objects implementing Traversable or ApiModelResponseItemInterface but :class provided', [
            ':class' => get_class($object),
        ]);
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface     $method
     * @param array|\Traversable                  $traversable
     * @param \DateTime                           $lastModified
     * @param \BetaKiller\Model\UserInterface     $user
     * @param \BetaKiller\Model\LanguageInterface $lang
     *
     * @return array
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultTraversable(
        ApiMethodInterface $method,
        $traversable,
        DateTime $lastModified,
        UserInterface $user,
        LanguageInterface $lang
    ): array {
        $data = [];

        foreach ($traversable as $key => $value) {
            $data[$key] = $this->convertResult($method, $value, $lastModified, $user, $lang);
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
        $type = gettype($data);

        if (!in_array(strtolower($type), static::$allowedResultTypes, true)) {
            throw new ApiMethodException('API model must not return values of type :type', [':type' => $type]);
        }

        return $data;
    }
}
