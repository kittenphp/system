<?php


namespace kitten\system\core;


use kitten\system\config\AppConfig;
use Symfony\Component\HttpFoundation\Request;

class ExceptionWrite implements ExceptionWriteInterface
{
    protected $logCatalog;
    /** @var Request  */
    protected $request;
    protected $appOption;
    public function __construct(AppConfig $appOption, Request $request)
    {
        $this->appOption=$appOption;
        $this->request=$request;
        $this->logCatalog=$appOption->getErrorLogCatalog();
    }

    public function write($exception){
        $dateTime=new \DateTime();
        $f=$this->createFile($dateTime);
        if (!empty($f)){
            $request=$this->request;
            $ip=$request->getClientIp();
            $url=$request->getUri();
            $method=$request->getMethod();
            $time=$dateTime->format('Y-m-d H:i:s');
            $post='';
            if ($request->getRealMethod()=='POST'){
                $formValues=$request->request->all();
                $post=implode(",", $formValues);
                $post='POST Values:'.$post;
            }
            $log="{$time}   ip:{$ip}   url:{$url}  method:{$method}  {$post} {$exception}";
            $log = str_ireplace(array("\r","\n",'\r','\n'),'', $log);
            file_put_contents($f,$log."\r\n",FILE_APPEND);
        }
    }
    protected function createFile(\DateTime $dateTime){
        $d=$this->createCatalog($dateTime);
        if (empty($d)){
            return '';
        }else{
            $f=$d.'/log-'.$dateTime->format('Y-m-d').'.txt';
            return $f;
        }
    }
    protected function createCatalog(\DateTime $dateTime){
        $year=$dateTime->format('Y');
        $month=$dateTime->format('m');
        $d=$this->logCatalog.'/'.$year.'/'.$month;
        if (is_dir($d)){
            return $d;
        }else{
            $isSuccess= mkdir($d,0777,true);
            if ($isSuccess){
                return $d;
            }else{
                return '';
            }
        }
    }
}