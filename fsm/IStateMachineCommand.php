<?php
namespace app\fsm;

use Illuminate\Support\Collection;

/**
 * Interface IStateMachineCommand
 *
 * @package app\fsm
 */
interface IStateMachineCommand
{
    /**
     * @param IStateMachineContext $context
     */
    public function run(IStateMachineContext $context);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getData($key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value);

    /**
     * @return Collection
     */
    public function getDataSet();

    /**
     * @param Collection|array|null $dataSet
     */
    public function setDataSet($dataSet);
}