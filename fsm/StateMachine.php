<?php
namespace app\fsm;

use app\fsm\providers\IStateMachineProvider;
use Illuminate\Support\Collection;

/**
 * Class StateMachine
 * Technically a collection of states with some extras
 *
 * @package app\fsm
 */
class StateMachine implements IStateMachine
{
    /**
     * @var IStateMachineState[]|Collection
     */
    private $states;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $initialStateValue;

    /**
     * @var IStateMachineProvider
     */
    private $provider;

    /**
     * StateMachine constructor.
     *
     * StateMachine Providers
     * if the provider can partially deliver (lazy load) the fragments
     * of the state machine then it should register itself as a provider. Otherwise the provider should
     * pre-build the State Machine and set the provider to null.
     *
     * @param IStateMachineProvider|null $provider
     */
    public function __construct($provider = null)
    {
        $this->provider = $provider;
        $this->states = new Collection();
    }

    /**
     * @param string $value
     * @return IStateMachineState
     * @throws EInvalidConfiguration
     * @throws EStateNotFound
     * @throws EInvalidParameter
     */
    public function getState($value)
    {
        if (empty($value)) {
            throw new EInvalidParameter("Cannot get state with empty name on StateMachine '{$this->name}'.");
        }

        if (!isset($this->states[$value])) {
            // If we have a provider try to load (create) the state (failure will raise EStateNotFound)
            // No provider then => dead end, state does not exist
            if ($this->provider) {
                $this->addState($this->provider->createState($value));
            } else {
                throw new EStateNotFound("State '{$value}' not found in StateMachine '{$this->name}'.");
            }
        }

        // return the state
        return $this->states[$value];
    }

    /**
     * This will force all the states to be loaded into memory.
     *
     * @return IStateMachineState[]|Collection
     * @throws EInvalidConfiguration
     * @throws EStateNotFound
     * @throws EInvalidParameter
     */
    public function getStates()
    {
        if ($this->provider) {
            // Preload all states
            foreach ($this->provider->enumStates() as $enumState) {
                $this->getState($enumState);
            }

            $this->provider = null;
        }

        return $this->states;
    }

    /**
     * @param IStateMachineState[] $states
     * @return $this
     */
    public function setStates($states)
    {
        $this->states = (new Collection($states))->keyBy('value');
        return $this;
    }

    /**
     * @param IStateMachineState $state
     * @return $this
     */
    public function addState(IStateMachineState $state)
    {
        if (!$this->states) {
            $this->states = new Collection();
        }

        $this->states[$state->getValue()] = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getInitialStateValue()
    {
        return $this->initialStateValue;
    }

    /**
     * @param string $initialStateValue
     * @return $this
     */
    public function setInitialStateValue($initialStateValue)
    {
        $this->initialStateValue = $initialStateValue;
        return $this;
    }
}