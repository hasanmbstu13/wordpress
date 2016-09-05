<?php

class CmsLoader {

    private $uri;
    private $key;

    /**
     * @param $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Create bridge file 
     */
    public function run()
    {
        $body =  $this->initCurl();
        foreach ($body->files as $key=>$content) {
            $this->write(explode('/',$key)[1],$content);          
        }
        $this->setRootLevel(4);
    }

    /**
     * Get bridge json
     * @return array|mixed|object
     */
    public function initCurl ()
    {
        $ch = curl_init();
        $query = http_build_query(array(
            'accessKey' => $this->key
        ));

        curl_setopt($ch, CURLOPT_URL, $this->uri. '?' . $query);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = @json_decode(curl_exec($ch));
        curl_close($ch);

        return $data;
    }

    /** 
     * Create file with root level
     * @param $level
     */
    public function setRootLevel($level)
    {
        $this->write('root_level.txt',$level);       
    }

    /**
     * Write files
     * @param $filePath
     * @param $fileContent
     */
    public function write($filePath, $fileContent)
    {
        $fileName = dirname(__FILE__).'/'.$filePath;
        $file = fopen($fileName, "x");
        fwrite($file, $fileContent);
        fclose($file);
    }
}
