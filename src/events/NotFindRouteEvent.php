<?php


namespace kitten\system\events;


use Symfony\Component\HttpFoundation\Response;

class NotFindRouteEvent extends BaseEvent
{
    /** @var Response */
     protected $response;

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}