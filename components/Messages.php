<?php
/**
 * User: Paris Theofanidis
 * Date: 02/07/16
 * Time: 14:44
 */
namespace app\components;

/**
 * Class Messages
 *
 * @package app\components
 */
class Messages
{
    const TYPE_DEFAULT = 0;
    const TYPE_ERROR = 1;

    /**
     * @var Message[]
     */
    private static $messages = [];

    /**
     * @param string|array $messages
     * @param int $type
     * @param bool $indentChildren
     */
    public static function add($messages, $type = self::TYPE_DEFAULT, $indentChildren = true)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        $i = 0;
        foreach ($messages as $message) {
            static::$messages[] = new Message($message, $type, $indentChildren && $i > 0);
            $i++;
        }
    }

    /**
     * @return Message[]
     */
    public static function get()
    {
        return static::$messages;
    }
}