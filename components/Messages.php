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
    /**
     * @var array
     */
    private static $messages = [];

    /**
     * @param $message
     */
    public static function add($message)
    {
        static::$messages[] = $message;
    }

    /**
     * @return array
     */
    public static function get()
    {
        return static::$messages;
    }
}