<?php

/**
 * Класс управления сессиями
 *
 * @author Mark Kreine
 * @package E-Clinic
 * @subpackage Session
 * @version 0.1
 * @copyright Mark Kreine 2012
 */
class Session
{
    /**
     * ID сессии
     *
     * @var string
     * @access protected
     */
    protected $session_id;

    /**
     * Браузер
     *
     * @var string
     * @access protected
     */
    protected $session_browser;

    /**
     * IP сессии
     *
     * @var string
     * @access protected
     */
    protected $session_ip;

    /**
     * Флаг, стартовала ли уже сессия
     *
     */
    public $isStarted;

    public function __construct()
    {

    }

    /**
     * Стартует сессию
     */
    public function start()
    {
        session_start();
        $this->session_ip       =   $_SERVER['REMOTE_ADDR'];
        $this->session_id       =   session_id();
        $this->session_browser  =   $_SERVER['HTTP_USER_AGENT'];
    }
}

?>
