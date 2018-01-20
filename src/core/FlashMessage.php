<?php


namespace kitten\system\core;


use Symfony\Component\HttpFoundation\Session\Session;

class FlashMessage
{
    protected $flashBag;
    public function __construct(Session $session)
    {
        $this->flashBag=$session->getFlashBag();
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function set(string $type,string $message){
        $this->flashBag->set($type,$message);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function has(string $type) {
        return $this->flashBag->has($type);
    }

    /**
     * @param string $type
     * @return string
     */
    public function get(string $type) {
        if (!$this->has($type)){
            return '';
        }else{
            $array=$this->flashBag->get($type);
            $result= reset($array);
            return $result;
        }
    }

    /**
     * Gets and clears flashes from the stack.
     * @return array
     */
    public function all() {
        return $this->flashBag->all();
    }
}