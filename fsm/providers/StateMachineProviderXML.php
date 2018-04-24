<?php
namespace app\fsm\providers;

use app\fsm\ETransitionNotFound;
use app\fsm\EInvalidConfiguration;
use app\fsm\EStateNotFound;
use app\fsm\IStateMachine;
use app\fsm\IStateMachineCommand;
use app\fsm\IStateMachineTransition;
use app\fsm\IStateMachineState;
use app\fsm\StateMachine;
use app\fsm\StateMachineCommand;
use app\fsm\StateMachineState;
use app\fsm\StateMachineTransition;
use SimpleXMLElement;

/**
 * Class StateMachineProviderXML
 *
 * This provider can form state machines out of an XML configuration.
 * The XML document can contain multiple state machines definitions.
 * This provider is ideal for very large state machines as it supports
 * lazy loading. This means the state machine components (states, events, etc)
 * will load on demand. However the XML document will be fully parsed and loaded
 * in memory (simplexml)
 *
 * @package app\fsm\providers
 */
class StateMachineProviderXML implements IStateMachineProvider
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
     * Parent provider if we represent a subtree of a bigger document (or a smaller fragment of a StateMachine)
     * @var StateMachineProviderXML
     */
    private $subTreeOf;

    /**
     * @var SimpleXMLElement
     */
    private $xml;

    /**
     * @return string
     */
    public function getDefaultStateMachineClass()
    {
        return $this->subTreeOf ? $this->subTreeOf->getDefaultStateMachineClass() : $this->defaultStateMachineClass;
    }

    /**
     * @param string $defaultStateMachineClass
     * @return $this
     */
    public function setDefaultStateMachineClass($defaultStateMachineClass)
    {
        if ($this->subTreeOf) {
            $this->subTreeOf->setDefaultStateMachineClass($defaultStateMachineClass);
        } else {
            $this->defaultStateMachineClass = $defaultStateMachineClass;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultStateClass()
    {
        return $this->subTreeOf ? $this->subTreeOf->getDefaultStateClass() : $this->defaultStateClass;
    }

    /**
     * @param string $defaultStateClass
     * @return $this
     */
    public function setDefaultStateClass($defaultStateClass)
    {
        if ($this->subTreeOf) {
            $this->subTreeOf->setDefaultStateClass($defaultStateClass);
        } else {
            $this->defaultStateClass = $defaultStateClass;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultTransitionClass()
    {
        return $this->subTreeOf ? $this->subTreeOf->getDefaultTransitionClass() : $this->defaultTransitionClass;
    }

    /**
     * @param string $defaultTransitionClass
     * @return $this
     */
    public function setDefaultTransitionClass($defaultTransitionClass)
    {
        if ($this->subTreeOf) {
            $this->subTreeOf->setDefaultTransitionClass($defaultTransitionClass);
        } else {
            $this->defaultTransitionClass = $defaultTransitionClass;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultCommandClass()
    {
        return $this->subTreeOf ? $this->subTreeOf->getDefaultCommandClass() : $this->defaultCommandClass;
    }

    /**
     * @param string $defaultCommandClass
     * @return $this
     */
    public function setDefaultCommandClass($defaultCommandClass)
    {
        if ($this->subTreeOf) {
            $this->subTreeOf->setDefaultCommandClass($defaultCommandClass);
        } else {
            $this->defaultCommandClass = $defaultCommandClass;
        }

        return $this;
    }

    /**
     * Enumerate the states of this StateMachine
     * @return string[]
     */
    public function enumStates()
    {
        $rVal = [];
        foreach ($this->xml->xpath('state') as $node) {
            $rVal[] = (string)$node[0]->attributes()->value;
        }

        return $rVal;
    }

    /**
     * Enumerate the transitions of a State
     * @return string[]
     */
    public function enumTransitions()
    {
        $rVal = [];
        foreach ($this->xml->xpath('transition') as $node) {
            $rVal[] = (string)$node[0]->attributes()->value;
        }

        return $rVal;
    }

    /**
     * Enumerate the commands of a Transition
     * @return string[]
     */
    public function enumCommands()
    {
        $rVal = [];
        foreach ($this->xml->xpath('command') as $node) {
            $rVal[] = (string)$node[0]->attributes()->name;
        }

        return $rVal;
    }

    /**
     * Create and configure an IStateMachine
     * @param string $name
     * @return IStateMachine
     * @throws EInvalidConfiguration
     */
    public function createStateMachine($name)
    {
        $node = $this->getNode(sprintf('//state-machine[@name=\'%s\']', $name));
        if (!$node) {
            throw new EInvalidConfiguration("StateMachine '{$name}' not found");
        }

        $name = $this->getAttr($node, 'name');

        // Determine class type of StateMachine
        $class = $this->getAttr($node, 'class', $this->defaultStateMachineClass);

        $sm = new $class($this->createSubtreeProvider($node));
        if (!$sm instanceof IStateMachine) {
            throw new EInvalidConfiguration("StateMachine '{$name}' class '{$class}' is not instance of IStateMachine.");
        }

        $sm->setName($name);
        $sm->setInitialStateValue($this->getAttr($node, 'initialState'));

        return $sm;
    }

    /**
     * Create and configure an IStateMachineState
     * @param string $stateValue
     * @return IStateMachineState
     * @throws EInvalidConfiguration
     * @throws EStateNotFound
     */
    public function createState($stateValue)
    {
        $node = $this->getNode(sprintf('state[@value=\'%s\']', $stateValue));
        if (!$node) {
            throw new EStateNotFound("State '{$stateValue}' does not exist");
        }

        // Determine class type of State
        $class = $this->extractAttr($node, 'class', $this->defaultStateClass);

        $state = new $class($this->createSubtreeProvider($node));
        if (!$state instanceof IStateMachineState) {
            throw new EInvalidConfiguration("State class '{$class}' is not instance of IStateMachineState.");
        }

        $state->setValue($this->extractAttr($node, 'value'));
        /** @noinspection PhpUndefinedFieldInspection */
        $state->setEnterCommands($this->createCommands($node->enter));
        /** @noinspection PhpUndefinedFieldInspection */
        $state->setExitCommands($this->createCommands($node->exit));

        // Extract all Node data into array
        $data = @(array)((array)$node->attributes());

        // Eliminate known tags
        unset($data['enter']);
        unset($data['exit']);
        unset($data['transition']);
        unset($data['timeout']);

        // Move the @attributes entries into array root
        if (isset($data['@attributes'])) {
            $data = array_merge($data, $data['@attributes']);
            unset($data['@attributes']);
        }
        $state->setDataSet($data);

        return $state;
    }

    /**
     * Create and configure an IStateMachineTransition
     * @param string $transitionValue
     * @return IStateMachineTransition
     * @throws EInvalidConfiguration
     * @throws ETransitionNotFound
     */
    public function createTransition($transitionValue)
    {
        $node = $this->getNode(sprintf('transition[@value=\'%s\']', $transitionValue));
        if (!$node) {
            throw new ETransitionNotFound("Transition '{$transitionValue}' does not exist");
        }

        // Determine class type of Transition
        $class = $this->extractAttr($node, 'class', $this->defaultTransitionClass);

        $transition = new $class($this->createSubtreeProvider($node));
        if (!$transition instanceof IStateMachineTransition) {
            throw new EInvalidConfiguration("Transition class '{$class}' is not instance of IStateMachineTransition.");
        }

        $transition->setValue($this->extractAttr($node, 'value'));
        $transition->setTarget($this->extractAttr($node, 'target'));

        // Extract all Node data into array
        $data = @(array)((array)$node->attributes());

        // Move the @attributes entries into array root
        if (isset($data['@attributes'])) {
            $data = array_merge($data, $data['@attributes']);
            unset($data['@attributes']);
        }
        $transition->setDataSet($data);

        return $transition;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return IStateMachineCommand[]
     * @throws EInvalidConfiguration
     */
    protected function createCommands(SimpleXMLElement $xml)
    {
        $rVal = [];
        $nodes = $xml->xpath('command');
        foreach ($nodes as $node) {
            $class = $this->extractAttr($node, 'class', $this->defaultCommandClass);
            $command = new $class();
            if (!$command instanceof IStateMachineCommand) {
                throw new EInvalidConfiguration("Command class '{$class}' is not instance of IStateMachineCommand.");
            }

            // Extract all Node data into array
            $data = @(array)((array)$node->attributes());

            // Move the @attributes entries into array root
            if (isset($data['@attributes'])) {
                $data = array_merge($data, $data['@attributes']);
                unset($data['@attributes']);
            }
            $command->setDataSet($data);

            $rVal[] = $command;
        }

        return $rVal;
    }

    /**
     * @param string $xpath
     * @return SimpleXMLElement|null
     */
    protected function getNode($xpath)
    {
        $nodes = $this->xml->xpath($xpath);
        return isset($nodes[0]) ? $nodes[0] : null;
    }

    /**
     * Will DELETE the attribute from the node and return the value
     * @param SimpleXMLElement $node
     * @param string $name
     * @param mixed $default
     * @return string|null
     */
    protected function extractAttr($node, $name, $default = null)
    {
        $nodes = $node->xpath(sprintf('@%s', $name));
        if (!isset($nodes[0])) {
            return $default;
        }

        $rVal = (string)$nodes[0];
        unset($node[0][$name]);

        return $rVal;
    }

    /**
     * Will return the value of the node's attribute
     * @param SimpleXMLElement $node
     * @param string $name
     * @param mixed $default
     * @return string|null
     */
    protected function getAttr($node, $name, $default = null)
    {
        $nodes = $node->xpath(sprintf('@%s', $name));
        if (!isset($nodes[0])) {
            return $default;
        }

        return (string)$nodes[0];
    }

    /**
     * Used internally to create providers of subtrees
     *
     * @param SimpleXMLElement $node
     * @return static
     */
    private function createSubtreeProvider(SimpleXMLElement $node)
    {
        $rVal = new static();
        $rVal->xml = $node;
        $rVal->subTreeOf = $this;
        return $rVal;
    }

    /**
     * Factory; create provider from an XML string
     * @param string $xmlString
     * @return static
     */
    public static function fromString($xmlString)
    {
        $rVal = new static();
        $rVal->xml = simplexml_load_file($xmlString);

        return $rVal;
    }

    /**
     * Factory; create provider from an XML file
     * @param string $filename
     * @return static
     */
    public static function fromFile($filename)
    {
        $rVal = new static();
        $rVal->xml = simplexml_load_file($filename);

        return $rVal;
    }
}