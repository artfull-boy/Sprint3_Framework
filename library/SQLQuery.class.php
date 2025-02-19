        <?php

        class SQLQuery
        {
                protected $conn;
                protected $_result;
                protected $_table;

                /** Connects to database **/
                function connect($host, $username, $password, $dbname)
                {
                        $dsn = "mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8mb4";

                        try {
                                $this->conn = new PDO($dsn, $username, $password);
                                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                error_log("Connection successful") ;
                                return true;
                        } catch (PDOException $e) {
                                error_log("Connection error: " . $e->getMessage());
                                return false;
                        }
                }

                /** Disconnects from database **/
                function disconnect()
                {
                        $this->conn = null;
                }

                /** Select all rows from the table **/
                function selectAll()
                {
                        if (!$this->conn) {
                                return false;
                        }
                        $query = 'SELECT * FROM `' . $this->_table . '`';
                        return $this->query($query);
                }

                /** Select a specific row by ID **/
                function select($id)
                {
                        if (!$this->conn) {
                                return false;
                        }
                        $query = 'SELECT * FROM `' . $this->_table . '` WHERE `id` = :id';
                        return $this->query($query, ['id' => $id], 1);
                }

                /** Custom SQL Query **/
                public function query($query, $params = [], $singleResult = false)
                {
                        if (!$this->conn) {
                                return false;
                        }
                        try {
                                $stmt = $this->conn->prepare($query);
                                $stmt->execute($params);
                                $this->_result = $stmt;

                                if (preg_match("/^SELECT/i", trim($query))) {
                                        return $singleResult ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                                return true;
                        } catch (PDOException $e) {
                                error_log("Query error: " . $e->getMessage());
                                return false;
                        }
                }

                /** Get number of rows **/
                function getNumRows()
                {
                        return ($this->_result) ? $this->_result->rowCount() : 0;
                }

                /** Free resources allocated by a query **/
                function freeResult()
                {
                        $this->_result = null;
                }

                /** Get error string **/
                function getError()
                {
                        return $this->conn ? $this->conn->errorInfo() : null;
                }
        }
