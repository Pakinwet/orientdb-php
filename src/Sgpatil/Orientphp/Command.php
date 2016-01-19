<?php

namespace Sgpatil\Orientphp;

/**
 * Abstract the parameters needed to make a request and parse the response
 */
abstract class Command {

    protected $client;

    /**
     * Set the client
     *
     * @param Client $client
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Return the data to pass
     *
     * @return mixed
     */
    abstract protected function getData();

    /**
     * Return the transport method to call
     *
     * @return string
     */
    abstract protected function getMethod();

    /**
     * Return the path to use
     *
     * @return string
     */
    abstract protected function getPath();

    abstract protected function getCommand();

    /**
     * Use the results in some way
     *
     * @param integer $code
     * @param array   $headers
     * @param array   $data
     * @return mixed
     * @throws Exception on failure
     */
    abstract protected function handleResult($code, $headers, $data);

    /**
     * Run the command and return a value signalling the result
     *
     * @return mixed
     * @throws Exception on failure
     */
    public function execute() {
        $method = $this->getMethod();
        $path = $this->getPath();
        $data = $this->getData();
        $command = $this->getCommand();
        $this->getTransport()->setCommand($command);
        $result = $this->getTransport()->$method($path, $data, $command);
        $resultCode = isset($result['code']) ? $result['code'] : Client::ErrorUnknown;
        $resultHeaders = isset($result['headers']) ? $result['headers'] : array();
        $resultData = isset($result['data']) ? $result['data'] : array();
        $parseResult = $this->handleResult($resultCode, $resultHeaders, $resultData);

        return $parseResult;
    }

    /**
     * Get the entity cache
     *
     * @return Cache\EntityCache
     */
    protected function getEntityCache() {
        return $this->client->getEntityCache();
    }

    /**
     * Get the entity mapper
     *
     * @return EntityMapper
     */
    protected function getEntityMapper() {
        return $this->client->getEntityMapper();
    }

    /**
     * Get the transport
     *
     * @return Transport
     */
    protected function getTransport() {
        return $this->client->getTransport();
    }

    /**
     * Throw an exception from handling the results
     *
     * @param string  $message
     * @param integer $code
     * @param array   $headers
     * @param array   $data
     * @throws Exception
     */
    protected function throwException($message, $code, $headers, $data) {
        $reason = "";
        if (isset($data['errors'])) {
            $reason = substr($data['errors'][0]['content'], strpos($data['errors'][0]['content'], ":") + 1);
        }
        $message = "{$message} [{$code}]:{$reason}"; // ."\nBody: " . print_r($data, true);
        throw new Exception($message, $code, $headers, $data);
    }

    /**
     * Throw an exception from handling the results
     *
     * @param string  $message
     * @param integer $code
     * @param array   $headers
     * @param array   $data
     * @throws Exception
     */
    protected function throwError($message, $code, $headers, $data) {
        $message = "Error: " . $message;
        throw new Exception($message, $code, $headers, $data);
    }

}
