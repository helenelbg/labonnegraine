<?php

/**
 * Class ColissimoCreateCustomDocumentResponse
 */
class ColissimoCreateCustomDocumentResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string $errorCode */
    public $errorCode;
    
    /** @var string $errorLabel */
    public $errorLabel;
    
    /** @var array $errors */
    public $errors;
    
    /** @var string $documentId */
    public $documentId;


    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $customDocumentResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $customDocumentResponse->errorCode = $responseArray['errorCode'];
            $customDocumentResponse->errorLabel = $responseArray['errorLabel'];
            if (isset($responseArray['errors']) && $responseArray['errors']) {
                $customDocumentResponse->errors = $responseArray['errors'];
            }
            if (isset($responseArray['documentId'])) {
                $customDocumentResponse->documentId = $responseArray['documentId'];
            }
        }

        return $customDocumentResponse;
    } 
}
