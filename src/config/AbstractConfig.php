<?php


namespace kitten\system\config;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfig
{
    protected $options=[];
    /** @var Filesystem  */
    protected $filesystem;
    public function __construct(array $options = [])
    {
        $this->filesystem=new Filesystem();
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }
    public abstract function configureOptions(OptionsResolver $resolver);

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function standardFileName(string $fileName) {
        $fileName=str_replace('\\','/',$fileName);
        return $fileName;
    }

    /**
     * @param string $directoryName
     * @return string
     */
    protected function standardDirectoryName(string $directoryName) {
        $directoryName=str_replace('\\','/',$directoryName);
        $directoryName=rtrim($directoryName,'/');
        return $directoryName;
    }

    /**
     * @param string $question
     * @return bool
     */
    protected function isNullOrEmptyString(string $question){
        return (!isset($question) || trim($question)==='');
    }

    /**
     * @param string $optionName
     * @param string $value
     * @param bool $allowNull
     * @return bool
     */
    protected function checkFile(string $optionName,string $value,bool $allowNull=true) {
        $fs=$this->filesystem;
        if ($allowNull && $this->isNullOrEmptyString($value)){
            return true;
        }else{
            if (!$fs->isAbsolutePath($value)){
                throw new ConfigException("{$optionName}:the configuration item is not an absolute path to a file.");
            }else{
                if (is_file($value)){
                    return true;
                }else{
                    throw new ConfigException("config:{$optionName} {$value}:The file does not exist");
                }
            }
        }
    }

    /**
     * @param string $optionName
     * @param string $value
     * @param bool $allowNull
     * @return bool
     */
    protected function checkDirectory(string $optionName,string $value,bool $allowNull=true) {
        $fs=$this->filesystem;
        if ($allowNull && $this->isNullOrEmptyString($value)){
            return true;
        }else{
            if (!$fs->isAbsolutePath($value)){
                throw new ConfigException("{$optionName}:the configuration item is not an absolute path to a directory.");
            }else{
                if (is_dir($value)){
                    return true;
                }else{
                    throw new ConfigException("config:{$optionName} {$value}:The directory does not exist");
                }
            }
        }
    }
}