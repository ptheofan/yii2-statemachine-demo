<?php
namespace app\fsm;

use app\fsm\providers\IStateMachineProvider;
use Illuminate\Support\Collection;

/**
 * Class StateMachineState
 * A collection of transitions with a couple of additional properties
 *
 * @package app\fsm
 */
class StateMachineState implements IStateMachineState
{
    use TStateMachineDataSetAccessibility;

    /**
     * @var IStateMachineTransition[]|Collection
     */
    private $transitions;

    /**
     * @var string
     */
    private $value;

    /**
     * @var IStateMachineProvider
     */
    private $provider;

    /**
     * @var IStateMachineCommand[]
     */
    private $enterCommands;

    /**
     * @var IStateMachineCommand[]
     */
    private $exitCommands;

    /**
     * StateMachineState constructor.
     *
     * StateMachine Providers
     * if the provider can partially deliver (lazy load) the fragments of a State
     * then it should register itself as a provider. Otherwise the provider should
     * pre-build the State and set the provider parameter to null.
     *
     * @param IStateMachineProvider $provider
     */
    public function __construct($provider = null)
    {
        $this->provider = $provider;
        $this->transitions = new Collection();
        $this->dataSet = new Collection();
        $this->enterCommands = [];
        $this->exitCommands = [];
    }

    /**
     * @return IStateMachineTransition[]|Collection
     * @throws EInvalidConfiguration
     * @throws ETransitionNotFound
     * @throws EInvalidParameter
     */
    public function getTransitions()
    {
        if ($this->provider) {
            foreach ($this->provider->enumTransitions() as $enumStateTransition) {
                $this->getTransition($enumStateTransition);
            }

            $this->provider = null;
        }

        return $this->transitions;
    }

    /**
     * @param string $value - the value of the transition
     * @return IStateMachineTransition|mixed
     * @throws ETransitionNotFound
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     */
    public function getTransition($value)
    {
        if (empty($value)) {
            throw new EInvalidParameter("Cannot get transition with empty name on State '{$this->getValue()}'.");
        }

        if (!isset($this->transitions[$value])) {
            // If we have a provider try to load (create) the transition (failure will raise ETransitionNotFound)
            // No provider then => dead end, transition does not exist
            if ($this->provider) {
                $this->addTransition($this->provider->createTransition($value));
            } else {
                throw new ETransitionNotFound("Transition '{$value}' not found in State '{$this->getValue()}'");
            }
        }

        // return the transition
        return $this->transitions[$value];
    }

    /**
     * @param IStateMachineTransition[]|Collection $transitions
     * @return $this
     */
    public function setTransitions($transitions)
    {
        if ($transitions instanceof Collection) {
            $this->transitions = $transitions;
        } elseif (is_array($transitions)) {
            $this->transitions = new Collection($transitions);
        } elseif ($transitions === null) {
            $this->transitions = null;
        } else {
            throw new \InvalidArgumentException("Invalid data type for \$data (".gettype($transitions).")");
        }
        return $this;
    }

    /**
     * @param IStateMachineTransition $transition
     * @return $this
     */
    public function addTransition(IStateMachineTransition $transition)
    {
        if (!$this->transitions) {
            $this->transitions = new Collection();
        }

        $this->transitions[$transition->getValue()] = $transition;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return IStateMachineCommand[]
     */
    public function getEnterCommands()
    {
        return $this->enterCommands;
    }

    /**
     * @param IStateMachineCommand[] $enterCommands
     * @return $this
     */
    public function setEnterCommands(array $enterCommands)
    {
        $this->enterCommands = $enterCommands;
        return $this;
    }

    /**
     * @return IStateMachineCommand[]
     */
    public function getExitCommands()
    {
        return $this->exitCommands;
    }

    /**
     * @param IStateMachineCommand[] $exitCommands
     * @return $this
     */
    public function setExitCommands(array $exitCommands)
    {
        $this->exitCommands = $exitCommands;
        return $this;
    }
}