<?php

class fileTransferRequestChunkedEnd
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var string $fileid
     */
    protected $fileid = null;

    /**
     * @param string $secKey
     * @param string $fileid
     */
    public function __construct($secKey, $fileid)
    {
      $this->secKey = $secKey;
      $this->fileid = $fileid;
    }

    /**
     * @return string
     */
    public function getSecKey()
    {
      return $this->secKey;
    }

    /**
     * @param string $secKey
     * @return fileTransferRequestChunkedEnd
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getFileid()
    {
      return $this->fileid;
    }

    /**
     * @param string $fileid
     * @return fileTransferRequestChunkedEnd
     */
    public function setFileid($fileid)
    {
      $this->fileid = $fileid;
      return $this;
    }

}
