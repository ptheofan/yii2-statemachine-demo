<?php
namespace app\fsm;

use Illuminate\Support\Collection;

/**
 * Interface IStateMachineTransition
 *
 * @package app\fsm
 */
interface IStateMachineTransition
{
    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

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

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @param string $target
     * @return $this
     */
    public function setTarget($target);
}