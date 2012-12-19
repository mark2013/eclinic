<?php

/**
 * Класс-наследник DB для работы с MySQL
 *
 * @author Mark Kreine
 * @copyright (c) 2012, Mark Kreine
 * @version 0.1
 * @package E-Clinic
 * @subpackage DB
 */
class MySQL extends DB {

    /**
     * Указатель на постоянное соединениние
     *
     * @var boolean
     * @access protected
     */
    protected $persistent;

    /**
     * Конструктор класса. Инициализирует переменные
     *
     * @return mixed
     * @access public
     */
    public function __construct()
    {
        $this->query_id = null;
        $this->persistent = false;

        $this->host             =   HOST;
        $this->user_name        =   USER;
        $this->user_password    =   PASS;
        $this->dbname           =   DB;

        if ($this->persistent)
        {
            $function = 'mysql_pconnect';
        }
        else
        {
            $function = 'mysql_connect';
        }

        $this->connection_id = $function($this->host, $this->user_name, $this->user_password);

        if (!is_resource($this->connection_id))
        {
            return false;
        }

        if ($this->databaseExists())
        {
            mysql_select_db($this->dbname);
        }

        else
        {
            die('The selected database does not exist');
        }
        return $this->connection_id;
    }

    /**
     * Установка или отключение постоянного соединения
     *
     * @param boolean $persistent Установить или отключить постоянное соединение
     * @return boolean
     * @access public
     */
    public function setPersistent($persistent)
    {
        if (!is_bool($persistent))
        {
            return false;
        }
        else
        {
            $this->persistent   =   $persistent;
        }
    }

    /**
     * Экранирует текст для последующей передачи в запрос
     *
     * @param string $unescaped_text Текст, который необходимо экранировать
     * @return mixed
     * @access public
     */
    public function escape($unescaped_text)
    {
        if (empty($unescaped_text))
        {
            return false;
        }
        else
        {
            return mysql_real_escape_string($unescaped_text);
        }
    }

    /**
     * Устанавливает текст последней ошибки
     *
     * @return void
     * @access protected
     */
    protected function setLastError()
    {
        $this->sql_error    =   mysql_error();
    }

    /**
     * Устанавливает номер последней ошибки
     *
     * @return void
     * @access protected
     */
    protected function setLastErrno()
    {
        $this->sql_errno    =   mysql_errno();
    }

    /**
     * Определяет, была ли ошибка MySQL
     *
     * @return boolean
     * @access public
     */
    public function hasError()
    {
        if (empty($this->sql_errno))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Вытаскивает последнюю ошибку сервера
     *
     * @access public
     * @return string
     */
    public function getLastError()
    {
        return $this->sql_error;
    }

    /**
     * Исполняет запрос
     *
     * @param string $query Текст запроса
     * @return void
     * @access public
     */
    public function query($query)
    {
        if (empty($query))
        {
            return false;
        }

        $this->query_id     = mysql_query($query);
        $this->setLastError();

        if (empty($this->sql_error))
        {
            return $this->query_id;
        }
    }

    /**
     * Анализирует запрос и выдаёт результаты в качестве массива
     *
     * @return mixed
     * @access public
     */
    public function fetchArray()
    {
        if ($this->hasError() || is_null($this->query_id))
        {
            return false;
        }

        return mysql_fetch_array($this->query_id);
    }

    /**
     * Анализирует запрос и выдаёт результаты в качестве свойств объекта
     *
     * @return mixed
     * @access public
     */
    public function fetchObject()
    {
        if ($this->hasError() || is_null($this->query_id))
        {
            return false;
        }

        return mysql_fetch_object($this->query_id);
    }

    /**
     * Анализирует запрос и выдаёт результаты в виде строки
     *
     * @return mixed
     * @access public
     */
    public function fetchRow()
    {
        if ($this->hasError() || is_null($this->query_id))
        {
            return false;
        }
        else
        {
            return mysql_fetch_row($this->query_id);
        }
    }

    /**
     * Считает строки, полученные в результате запроса SELECT
     *
     * @return mixed
     * @access public
     */
    public function countSelectRows()
    {
        if ($this->hasError() || is_null($this->query_id))
        {
            return false;
        }
        else
        {
            return mysql_num_rows($this->query_id);
        }
    }

    /**
     * Считает строки, модифицированные запросом
     *
     * @return mixed
     * @access public
     */
    public function countModifiedRows()
    {
        if ($this->hasError() || is_null($this->query_id))
        {
            return false;
        }
        else
        {
            return mysql_affected_rows();
        }
    }

    /**
     * Собирает список всех таблиц из базы
     *
     * @return mixed
     * @access protected
     */
    protected function getAllTables()
    {
        $tables = array();

        $this->query('SHOW TABLES from ' . $this->dbname);
        $var = 'Tables_in_'.$this->dbname;

        while ($result = $this->fetchArray())
        {
            $tables[] = $result[$var];
        }

        return $tables;
    }

    /**
     * Проверяет существование таблицы
     *
     * @param string $tableName Таблица, существование которой необходимо проверить
     * @return boolean
     * @access protected
     */
    protected function tableExists($tableName)
    {
        $tables = $this->getAllTables();

        if (!in_array($tableName, $tables))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Вытаскивает список баз данных с сервера
     *
     * @return array
     * @access protected
     */
    protected function getAllDatabases()
    {
        $dbs = array();

        $this->query('SHOW DATABASES');


        while ($result = $this->fetchArray())
        {
            $dbs[] = $result['Database'];
        }

        return $dbs;
    }

    /**
     * Проверяет существование базы данных. БД должна быть указана в свойстве класса
     *
     * @return boolean
     * @access protected
     */
    protected function databaseExists()
    {
        $dbs = $this->getAllDatabases();

        if (!in_array($this->dbname, $dbs))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}

/**
 * Класс-потомок MySQL, позволяющий работать с PDO
 *
 * @author Mark Kreine
 * @copyright (c) 2012, Mark Kreine
 * @version 0.1
 * @package E-Clinic
 * @subpackage DB
 */
class PDOMySQL extends MySQL
{

    protected $pdo_statement;
    /**
     * Конструктор класса PDOMySQL
     * @return mixed
     */
    public function __construct()
    {
        parent::__construct();
        $dsn = 'mysql:dbname='.$this->dbname.';host='.$this->host;
        $pdo = new PDO($dsn, $this->user_name, $this->user_password);

        if (!is_object($pdo))
        {
            return false;
        }
        else
        {
            return $pdo;
        }
    }

    /**
     * Экранирует строку
     *
     * @param string $unescaped_text Текст, который необходимо экранировать
     * @return string
     * @access public
     */
    public function escape($unescaped_text)
    {
           return PDO::quote($unescaped_text);
    }


    /**
     * Устанавливает текст последней ошибки
     *
     * @return void
     * @access protected
     */
    protected function setLastError()
    {
        $this->sql_error    =  PDO::errorInfo();
    }

    /**
     * Вытаскивает последнюю ошибку сервера
     *
     * @access public
     * @return string
     */
    public function getLastError()
    {
        return parent::getLastError();
    }


     /**
     * Устанавливает текст последней ошибки
     *
     * @return void
     * @access protected
     */
    protected function setLastErrno()
    {
        $this->sql_errno    =   PDO::errorCode();
    }

    /**
     * Выполняет запрос
     *
     * @param string $query Текст запроса
     * @return mixed
     */
    public function query($query)
    {
        if (empty($query))
        {
            return false;
        }

        $this->pdo_statement = PDO::query($query);
    }

    /**
     * Вытаскивает результаты запроса в виде массива
     *
     * @access public
     * @return mixed
     */
    public function fetchArray()
    {
        $statement = $this->pdo_statement;

        if (!($statement instanceof PDOStatement))
        {
            return false;
        }
        else
        {
            return $statement::fetchAll();
        }
    }

    /**
     * Вытаскивает результаты запроса в виде свойств соответствующего объекта
     *
     * @access public
     * @return boolean
     */
    public function fetchObject()
    {
        $statement = $this->pdo_statement;

        if (!($statement instanceof PDOStatement))
        {
            return false;
        }
        else
        {
            return $statement::fetchObject();
        }
    }

    /**
     * Вытаскивает результаты запроса в массив с индексами
     *
     * @access public
     * @return mixed
     */
    public function fetchRow()
    {
        $statement = $this->pdo_statement;

        if (!($statement instanceof PDOStatement))
        {
            return false;
        }
        else
        {
            return $statement::fetch(PDO::FETCH_NUM);
        }
    }

    /**
     * Считает строки, в результате запроса UPDATE, INSERT, DELETE
     *
     * @access public
     * @return mixed
     */
    public function countModifiedRows()
    {
        $statement = $this->pdo_statement;

        if (!($statement instanceof PDOStatement))
        {
            return false;
        }
        else
        {
            return $statement::rowCount();
        }
    }

    /**
     * Считает строки. полученные в результате запроса SELECT
     * @param string $query Текст запроса
     * @return integer
     */
    public function countSelectRows($query)
    {
           return PDO::exec($query);
    }
}

?>
