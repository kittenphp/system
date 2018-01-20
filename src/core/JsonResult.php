<?php


namespace kitten\system\core;


class JsonResult
{
    protected $success=false;
    protected $title='';
    protected $message='';
    protected $content=[];

    /**
     * JsonResult constructor.
     * @param bool $success
     * @param string $title
     * @param string $message
     * @param array $content
     */
    public function __construct(bool $success=true,string $title='',string $message='',array $content=[])
    {
        $this->success=$success;
        $this->title=$title;
        $this->message=$message;
        $this->content=$content;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param bool $success
     * @return $this
     */
    public function setSuccess(bool $success)
    {
        $this->success = $success;
        return $this;
    }


    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }
    public function addContent(string $key,$value){
        $this->content[$key]=$value;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        $array=[];
        $array['success']=$this->success;
        $array['title']=$this->title;
        $array['message']=$this->message;
        $array['content']=$this->content;
        return $array;
    }

    /**
     * @return string
     */
    public function toJson() {
        $array=$this->toArray();
        return json_encode($array);
    }
}