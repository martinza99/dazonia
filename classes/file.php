<?php

class File
{

    public $name;
    public $type;
    public $tmp_name;
    public $size;
    public $md5;
    public $hashname;

    public function __construct(String $name, String $type, String $tmp_name, int $size)
    {
        $this->name = $name;
        $this->type = $type;
        $this->tmp_name = $tmp_name;
        $this->size = $size;
        $this->md5 = md5_file($this->tmp_name);
        $this->hashname = $this->makeHashName();
    }

    /**
     * @return String return filename without extension
     */
    public function filename()
    {
        return pathinfo($this->name, PATHINFO_FILENAME);
    }
    /**
     * @return String returns extension
     */
    public function ext()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    private function makeHashName()
    {
        $md5 = md5_file($this->tmp_name, true);
        $b64 = base64_encode($md5);
        $b64 = str_replace(array("+", "/"), array("-", "_"), $b64);
        $u8 = utf8_decode($b64);
        return $u8;
    }
}
