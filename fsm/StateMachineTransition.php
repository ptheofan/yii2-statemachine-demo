<?php
namespace app\fsm;

use app\fsm\providers\IStateMachineProvider;
use Illuminate\Support\Collection;

/**
 * Class StateMachineTransition
 * A collection of commands and some other yummy stuff
 *
 * @package app\fsm
 */
class StateMachineTransition implements IStateMachineTransition
{
    use TStateMachineDataSetAccessibility;

    /**
     * @var string
     */
    private $value;

    /**
     * The value of the state this transition is linking to
     * @var string
     */
    private $target;

    /**
     * @var IStateMachineCommand[]|Collection
     */
    private $commands;

    /**
     * @var IStateMachineProvider|null
     */
    private $provider;

    /**
     * StateMachineTransition constructor.
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
        $this->dataSet = new Collection();
        $this->commands = new Collection();
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;
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
     * TODO: StateMachine Commands
     *
     * @return IStateMachineTransition[]|Collection
     * @throws EInvalidConfiguration
     * @throws ETransitionNotFound
     */
    public function getCommands()
    {
        // TODO: body
        if ($this->provider) {
            foreach ($this->provider->enumStateTransitions($this->getValue()) as $enumStateTransition) {
                $this->getCommand($enumStateTransition);
            }

            $this->provider = null;
        }

        return $this->transitions;
    }

    /**
     * TODO: StateMachine Commands
     *
     * @param string $name - the name of the transition
     * @return IStateMachineTransition|mixed
     * @throws ETransitionNotFound
     * @throws EInvalidConfiguration
     */
    public function getCommand($name)
    {
        if (!isset($this->transitions[$value])) {
            // If we have a provider try to load (create) the transition (failure will raise ETransitionNotFound)
            // No provider then => dead end, transition does not exist
            if ($this->provider) {
                $this->addTransition($this->provider->createTransition($this->getValue(), $value));
            } else {
                throw new ETransitionNotFound("Transition '{$value}' not found in State '{$this->getValue()}'");
            }
        }

        // return the transition
        return $this->transitions[$value];
    }

    /**
     * TODO: StateMachine Commands
     *
     * @param IStateMachineTransition[]|Collection $transitions
     * @return $this
     */
    public function setCommands($transitions)
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
     * TODO: StateMachine Commands
     *
     * @param IStateMachineTransition $transition
     * @return $this
     */
    public function addCommand(IStateMachineTransition $transition)
    {
        if (!$this->transitions) {
            $this->transitions = new Collection();
        }

        $this->transitions[$transition->getValue()] = $transition;
        return $this;
    }
}