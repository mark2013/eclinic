<?php

/**
 * Абстрактный класс работы с пользователями.
 * Класс делится в последующем на 2 подкласса: пациенты и врачи
 *
 * @abstract
 * @package E-Clinic
 * @subpackage Users
 * @author Mark Kreine
 * @copyright (c) 2012, Mark Kreine
 */
abstract class User
{
    /**
     * ID пользователяя
     *
     * @var integer
     * @access protected
     */
    protected $user_id;

    /**
     * ID группы пользователя
     *
     * @var integer
     * @access protected
     */
    protected $group_id;

    /**
     * Имя пользователя
     *
     * @var string
     * @access protected
     */
    protected $user_name;

    /**
     * Отчество пользователя
     *
     * @var string
     * @access protected
     */
    protected $user_middle_name;

    /**
     * Фамилия пользователя
     *
     * @var string
     * @access protected
     */
    protected $user_last_name;

    /**
     * Логин пользователя
     *
     * @var string
     * @access protected
     */
    protected $user_login;

    /**
     * Пароль пользователя
     *
     * @var string
     * @access protected
     */
    protected $user_password;

    /**
     * E-mail пользователя
     *
     * @var string
     * @access protected
     */
    protected $user_email;


    /**
     * Достаёт e-mail пользователя
     *
     * @return string
     * @access public
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * Проверяет e-mail на корректность
     *
     * @param string $email E-mail который необходимо проверить
     * @return boolean
     * @access protected
     */
    protected function checkEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Достаёт имя пользователя
     *
     * @access public
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Достаёт отчество пользователя
     *
     * @access public
     * @return string
     */
    public function getUserMiddleName()
    {
        return $this->user_middle_name;
    }

    /**
     * Достаёт фамилию пользователя
     *
     * @access public
     * @return string
     */
    public function getUserLastName()
    {
        return $this->user_last_name;
    }


    /**
     * Проверяет что пользователь действительно существует
     *
     * @static
     * @global object $sql
     * @param integer $user_id
     * @return boolean
     */
    public static function userExists($user_id)
    {
        global $sql;
        $sql->query('SELECT user_id FROM users WHERE user_id = ' . (int)$user_id);
        $rows = $sql->countSelectRows();

        if ($rows < 1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Достаёт группу пользователя
     *
     * @return mixed
     * @access public
     */
    public function getUserGroupID()
    {
        if (!self::userExists($this->user_id))
        {
            return false;
        }

        global $sql;

        $sql->query('SELECT group_id FROM users WHERE user_id = ' . (int)$this->user_id);
        $result = $sql->fetchArray();

        return (int)$result['group_id'];
    }

    /**
     * Устанавливает ID пользователя
     *
     * @param integer $user_id ID которое собираемся присвоить
     * @access public
     * @return void
     */
    protected function setUserID($user_id)
    {
        if (!self::userExists($user_id))
        {
            return false;
        }

        $this->user_id  =   (int)$user_id;
    }

    /**
     * Возвращает ID пользователя
     *
     * @access public
     * @return integer
     */
    public function getUserID()
    {
        return (int)$this->user_id;
    }

    /**
     * Изменяет пароль пользователю с id=$this->user_id на указанный в аргументе функции
     *
     * @param string $pass Пароль, на который требуется поменять
     * @return boolean
     */
    public function updatePassword($pass)
    {
        if (empty($pass))
        {
            return false;
        }

        global $sql;
        $sql->query('UPDATE users SET user_password = ' . $sql->escape(md5($pass)) . ' WHERE user_id = ' . (int)$this->user_id);
        $rows = $sql->countModifiedRows();
        if (is_int($rows) and ($rows > 0))
        {
            return true;
        }
        else
        {
            return false;
        }

    }
}

class Patient extends User
{
    /**
     * СНИЛС
     *
     * @var string
     * @access public
     */
    public $snils;

    /**
     * Компания медицинского страхования
     *
     * @var string
     * @access public
     */
    public $insurance_company;

    /**
     * Номер медицинского полиса
     *
     * @var string
     * @access public
     */
    public $insurance_doc_number;

    /**
     * Дата, до которой медицинский полис действителен
     *
     * @var string
     * @access public
     */
    public $insurance_date_valid_till;
}

?>
