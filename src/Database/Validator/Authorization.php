<?php

namespace Database\Validator;

use Database\Document;
use Utopia\Validator;

class Authorization extends Validator
{
    /**
     * @var array
     */
    protected static $roles = ['*'];

    /**
     * @var Document
     */
    protected $document = null;

    /**
     * @var string
     */
    protected $action = '';

    /**
     * @var string
     */
    protected $message = 'Unknown Error';

    /**
     * Structure constructor.
     *
     * @param Document $document
     * @param string   $action
     */
    public function __construct(Document $document, $action)
    {
        $this->document = $document;
        $this->action = $action;
    }

    /**
     * Get Description.
     *
     * Returns validator description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->message;
    }

    /**
     * Is valid.
     *
     * Returns true if valid or false if not.
     *
     * @param array $permissions
     *
     * @return bool
     */
    public function isValid($permissions)
    {
        if (!self::$status) {
            return true;
        }

        if (!isset($permissions[$this->action])) {
            $this->message = 'Missing action key: "'.$this->action.'"';

            return false;
        }

        $permission = null;

        foreach ($permissions[$this->action] as $permission) {
            $permission = str_replace(':{self}', ':'.$this->document->getUid(), $permission);

            if (in_array($permission, self::getRoles())) {
                return true;
            }
        }

        $this->message = 'User is missing '.$this->action.' for '.$permission.' permission. only this scope "'.json_encode(self::getRoles()).'" is given.';

        return false;
    }

    /**
     * @param string $role
     */
    public static function setRole($role)
    {
        self::$roles[] = $role;
    }

    /**
     * @return array
     */
    public static function getRoles()
    {
        return self::$roles;
    }

    /**
     * @var bool
     */
    public static $status = true;

    /**
     *
     */
    public static function enable()
    {
        self::$status = true;
    }

    /**
     *
     */
    public static function disable()
    {
        self::$status = false;
    }
}
