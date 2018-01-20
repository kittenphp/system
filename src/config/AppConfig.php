<?php


namespace kitten\system\config;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppConfig extends AbstractConfig
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'is_debug' => false,
            'error_page_404' => '',
            'error_page_500' => '',
            'error_log_dir' => '',
            'error_is_write' => true
        ]);
        $resolver->setAllowedTypes('is_debug', 'bool');
        $resolver->setAllowedTypes('error_page_404', 'string');
        $resolver->setAllowedTypes('error_page_500', 'string');
        $resolver->setAllowedTypes('error_log_dir', 'string');
        $resolver->setAllowedTypes('error_is_write', 'bool');
        $resolver->setNormalizer('error_page_404', function (Options $options, $value) {
            $value = $this->standardFileName($value);
            return $value;
        });
        $resolver->addAllowedValues('error_page_404', function ($value) {
            return $this->checkFile('error_page_404', $value, true);
        });
        $resolver->addAllowedValues('error_page_500', function ($value) {
            return $this->checkFile('error_page_500', $value, true);
        });
        $resolver->setNormalizer('error_page_500', function (Options $options, $value) {
            $value = $this->standardFileName($value);
            return $value;
        });
        $resolver->addAllowedValues('error_log_dir', function ($value) {
            return $this->checkDirectory('error_log_directory', $value, true);
        });
        $resolver->setNormalizer('error_log_dir', function (Options $options, $value) {
            $value = $this->standardDirectoryName($value);
            return $value;
        });
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->options['is_debug'];
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug = true)
    {
        $this->options['is_debug'] = $debug;
    }

    /**
     * @return string|null
     */
    public function getPage404()
    {
        return $this->options['error_page_404'];
    }

    /**
     * @return string|null
     */
    public function getPage500()
    {
        return $this->options['error_page_500'];
    }

    /**
     * @return string|null
     */
    public function getErrorLogCatalog()
    {
        return $this->options['error_log_dir'];
    }

    /**
     * @return bool
     */
    public function isWriteError()
    {
        return $this->options['error_is_write'];
    }
}