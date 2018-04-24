<?php
namespace app\fsm\providers;

use app\fsm\EInvalidConfiguration;
use app\fsm\IStateMachine;
use app\fsm\IStateMachineState;
use app\fsm\IStateMachineTransition;
use app\fsm\StateMachine;
use app\fsm\StateMachineCommand;
use app\fsm\StateMachineState;
use app\fsm\StateMachineTransition;

/**
 * Class StateMachineProviderArray
 *
 * @package app\fsm\providers
 */
class StateMachineProviderArray
{
    /**
     * @var string
     */
    private $defaultStateMachineClass = StateMachine::class;

    /**
     * @var string
     */
    private $defaultStateClass = StateMachineState::class;

    /**
     * @var string
     */
    private $defaultTransitionClass = StateMachineTransition::class;

    /**
     * @var string
     */
    private $defaultCommandClass = StateMachineCommand::class;

    /**
     * @return string
     */
    public function getDefaultStateMachineClass()
    {
        return $this->defaultStateMachineClass;
    }

    /**
     * @param string $defaultStateMachineClass
     * @return $this
     */
    public function setDefaultStateMachineClass($defaultStateMachineClass)
    {
        $this->defaultStateMachineClass = $defaultStateMachineClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultStateClass()
    {
        return $this->defaultStateClass;
    }

    /**
     * @param string $defaultStateClass
     * @return $this
     */
    public function setDefaultStateClass($defaultStateClass)
    {
        $this->defaultStateClass = $defaultStateClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultTransitionClass()
    {
        return $this->defaultTransitionClass;
    }

    /**
     * @param string $defaultTransitionClass
     * @return $this
     */
    public function setDefaultTransitionClass($defaultTransitionClass)
    {
        $this->defaultTransitionClass = $defaultTransitionClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultCommandClass()
    {
        return $this->defaultCommandClass;
    }

    /**
     * @param string $defaultCommandClass
     * @return $this
     */
    public function setDefaultCommandClass($defaultCommandClass)
    {
        $this->defaultCommandClass = $defaultCommandClass;
        return $this;
    }

    /**
     * @param string $name
     * @param array $config
     * @return IStateMachine
     * @throws EInvalidConfiguration
     */
    public function createStateMachine($name, array $config)
    {
        if (empty($config[$name])) {
            throw new EInvalidConfiguration("State Machine Config not found. Config does not contain key '{$name}'.");
        }

        $smClass = $this->getDefaultStateMachineClass();
        $stateClass = $this->getDefaultStateClass();
        $transitionClass = $this->getDefaultTransitionClass();

        /** @var IStateMachine $sm */
        $sm = new $smClass();
        $sm->setName($name);

        if (empty($config[$name]['@initialState'])) {
            throw new EInvalidConfiguration("StateMachine '{$name}' requires an @initialState.");
        }

        $sm->setInitialStateValue($config[$name]['@initialState']);
        unset($config[$name]['@initialState']);

        foreach ($config[$name] as $stateValue => $stateConfig) {
            /** @var IStateMachineState $state */
            $state = new $stateClass();
            $state->setValue($stateValue);

            foreach ($stateConfig as $transitionValue => $transitionConfig) {
                /** @var IStateMachineTransition $transition */
                $transition = new $transitionClass();
                $transition->setValue($transitionValue);

                if (is_string($transitionConfig)) {
                    $transition->setTarget($transitionConfig);
                } else {
                    if (empty($transitionConfig['target'])) {
                        throw new EInvalidConfiguration("Transition '{$transitionValue}' is missing target state value.");
                    }

                    $transition->setTarget($transitionConfig['target']);
                    unset($transitionConfig['target']);
                    $transition->setDataSet($transitionConfig);

                    // TODO: Commands
                }

                $state->addTransition($transition);
            }

            $sm->addState($state);
        }

        return $sm;
    }

}