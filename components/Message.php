<?php
/**
 * User: Paris Theofanidis
 * Date: 03/07/16
 * Time: 02:41
 */
namespace app\components;

use yii\bootstrap\Html;

/**
 * Class Message
 *
 * @package app\components
 */
class Message
{
    /**
     * @var string
     */
    private $msg;

    /**
     * @var int
     */
    private $type;

    /**
     * @var bool
     */
    private $indent = false;

    /**
     * Message constructor.
     *
     * @param string $msg
     * @param int $type
     * @param bool $indent
     */
    public function __construct($msg, $type, $indent)
    {
        $this->msg = $msg;
        $this->type = $type;
        $this->indent = $indent;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeClass()
    {
        switch ($this->getType()) {
            case Messages::TYPE_DEFAULT:
                return "default";
            case Messages::TYPE_ERROR:
                return "error";
            default:
                return 'default';
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->msg;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $classes = [$this->getTypeClass()];

        if ($this->indent) {
            $classes[] = 'indent';
        }

        return Html::tag('span', $this->getMsg(), ['class' => implode(' ', $classes)]);
    }
}