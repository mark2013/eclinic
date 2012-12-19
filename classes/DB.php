<?php

/**
 * Абстрактный класс-предок для всех классов, работающих с базами данных
 *
 * @abstract
 * @author Mark Kreine
 * @copyright (c) 2012, Mark Kreine
 * @version 0.1
 * @package E-Clinic
 * @subpackage DB
 */
class DB
{
    /**
     * Хост базы данных
     *
     * @var string
     * @access protected
     */
    protected $host;

    /**
     * Название базы данных
     *
     * @var string
     * @access protected
     */
    protected $dbname;

    /**
     * Имя пользователя для подключения к серверу
     *
     * @var string
     * @access protected
     */
    protected $user_name;

    /**
     * Пароль пользователя для подключения
     *
     * @var string
     * @access protected
     */
    protected $user_password;

    /**
     * ID соединения
     *
     * @var resource
     * @access protected
     */
    protected $connection_id;

    /**
     * ID запроса
     *
     * @var resource
     * @access protected
     */
    protected $query_id;

    /**
     * Текст запроса
     *
     * @var string
     * @access protected
     */
    protected $query_text;

    /**
     * Кол-во запросов
     *
     * @var integer
     * @access protected
     */
    protected $queries_num;

    /**
     * Последняя ошибка SQL
     *
     * @var string
     * @access protected
     */
    protected $sql_error;

    /**
     * Номер последней ошибки SQL
     *
     * @var integer
     * @access protected
     */
    protected $sql_errno;

    /**
     * Конструктор
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        /**
         * @ignore
         * Пустой конструктор
         */
    }

    /**
     * Вытаскивает хост
     *
     * @return string
     * @access public
     * @final
     */
    final public function getHost()
    {
        return $this->host;
    }

    /**
     * Вытаскивает базу данных
     *
     * @return string
     * @access public
     * @final
     */
    final public function getDBName()
    {
        return $this->dbname;
    }

    /**
     * Вытаскивает имя пользователя
     *
     * @var string
     * @access public
     * @final
     */
    final public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Вытаскивает пароль пользователя
     *
     * @param boolean $md5 Нужен ли пароль сразу в формате md5
     * @access public
     * @final
     */
    final public function getUserPassword($md5 = false)
    {
        if ($md5)
        {
            return md5($this->user_password);
        }
        else
        {
            return $this->user_password;
        }
    }

}
?>