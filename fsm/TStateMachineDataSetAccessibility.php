<?php
namespace app\fsm;

use Illuminate\Support\Collection;

/**
 * Trait TStateMachineDataSetAccessibility
 *
 * @package app\fsm
 */
trait TStateMachineDataSetAccessibility
{
    /**
     * Setters, Getters, etc are coming from
     * the trait TStateMachineDataSetAccessibility
     *
     * @var Collection
     */
    private $dataSet;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        if (!isset($this->dataSet[$key])) {
            return $default;
        }

        return $this->dataSet[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value)
    {
        $this->dataSet[$key] = $value;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * @param Collection|array|null $dataSet
     */
    public function setDataSet($dataSet)
    {
        if ($dataSet instanceof Collection) {
            $this->dataSet = $dataSet;
        } elseif (is_array($dataSet)) {
            $this->dataSet = new Collection($dataSet);
        } elseif ($dataSet === null) {
            $this->dataSet = new Collection();
        } else {
            throw new \InvalidArgumentException("Invalid data type for \$data (".gettype($dataSet).")");
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->dataSet[$name])) {
            return $this->dataSet[$name];
        }

        $reflection = new \ReflectionClass($this);
        $reflection->getShortName();
        throw new \InvalidArgumentException("StateMachine {$reflection->getShortName()} does not have any '{$name}' key in it's dataSet");
    }
}