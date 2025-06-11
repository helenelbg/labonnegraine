<?php

namespace Sc\service\Process\Traits;

trait ProcessWithPaginationTrait
{
    use ProcessTrait;

    /**
     * @var mixed
     */
    private $maxPage;
    /**
     * @var mixed
     */
    private $batchSize;

    /**
     * @param $data
     * @param $currentPage
     * @return bool
     */
    public function terminateOn($data, $currentPage)
    {
        return !(count($data) < $this->getBatchSize() // dernière page incomplète
            or $currentPage === $this->getMaxPage() - 1 // limitation nb page
        );
    }

    /**
     * @return int
     */
    public function getMaxPage()
    {
        return (int) $this->maxPage ?: 0;
    }

    /**
     * @param mixed $maxPage
     */
    public function setMaxPage($maxPage)
    {
        $this->maxPage = $maxPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return (int) $this->batchSize ?: 50;
    }

    /**
     * @param mixed $batchSize
     */
    public function setBatchSize($batchSize)
    {
        $this->batchSize = $batchSize;

        return $this;
    }
}
