<?php

/**
 * Class ColissimoUpdateCustomDocumentRequest
 */
class ColissimoUpdateCustomDocumentRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/api-document/rest/updatedocument?';
    const WS_CONTENT_TYPE = 'multipart/form-data';
    const WS_HEADER_DOCUMENT = 1;

    /** @var string $accountNumber */
    protected $accountNumber;

    /** @var int $parcelNumber */
    protected $parcelNumber;

    /** @var string $documentType */
    protected $documentType;

    /** @var binary $file */
    protected $file;

    /** @var string $filename */
    protected $filename;

    /** @var array $parcelNumberList */
    protected $parcelNumberList;

    /**
     * @param string $accountNumber
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @param string $parcelNumber
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    /**
     * @param string $documentType
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;

        return $this;
    }

    /**
     * @param array $file
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $filename
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }
            
    /**
     * @param array $parcelNumberList
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setParcelNumberList($parcelNumberList)
    {
        $this->parcelNumberList = $parcelNumberList;

        return $this;
    }    

    /**
     * @return mixed|void
     */
    public function buildRequest()
    {
        $this->request['accountNumber'] = $this->accountNumber;
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['documentType'] = $this->documentType;
        $this->request['file']= $this->file;
        $this->request['filename'] = $this->filename;
        if (!empty($this->parcelNumberList)) {
            $this->request['parcelNumberList'] = $this->parcelNumberList;
        }
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoUpdateCustomDocumentResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
