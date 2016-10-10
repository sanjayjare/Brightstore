<?php

    /**
     * Database Class
     *
     * Grants access to the default MySQL database
     *
     * @uses Utils::HandleError for error handling
     * @uses Utils::HandleComment for debug comment handling
     * @uses Utils::FormatMysqlError to strip out the leading text from mysqli_error
     */
    class PDO_db
    {
        /**
         * MySQL Database to connect to
         */
        protected $Database = '';

        /**
         * MySQL user to connect with
         */
        protected $User = '';

        /**
         * Password to connect with
         */
        protected $Password = '';

        /**
         * Host to connect to
         */
        protected $Host = '';

        /**
         * Secondary Host to connect to
         */
        protected $Host2 = '';

        /**
         * Connect using ssl
         */
        protected $SSL = false;

        /**
         * ca cert path
         */
        protected $CA_PATH = '/etc/mysql/ssl/';

        /**
         * Mysql connection
         */
        protected $Link = null;

        /**
         * Mysql PDO connection
         */
        protected $PDO_conn = null;

        /**
         * Execute only select queries and print all SQL
         */
        public $Debug = false;

        /**
         * Running count of all queries made
         */
        public $Count = 0;

        /**
         * Last insert ID from an insert query
         */
        public $ID = 0;

        /**
         * Holds an instance of this class
         */
        protected static $Instance;

        /**
         * Boolean for whether or not we need to call a charset('utf8') during the connect.
         */
        protected $Utf8;

        /**
         * Constructor
         *
         * Sets the default values for Database, Host, Username, and Password
         *
         * @param string $Database name of MySQL database to connect to
         * @param string $User name of MySQL user to connect with
         * @param string $Password password to connect with
         * @param string $Host host name to connect to
         */
        public function __construct( $Database=OQ_DB, $User=DATABASE_USER, $Password=DATABASE_PASSWORD, $Host=DATABASE_HOST, $Utf8='utf8mb4', $SSL=DB_SSL, $CA_PATH=CA_PATH, $Host2=DATABASE_HOST2 )
        {
            $this->Database = $Database;
            $this->Host = $Host;
            $this->Host2 = $Host2;
            $this->User = $User;
            $this->Password = $Password;
            $this->Utf8 = $Utf8;
            $this->SSL = $SSL;
            $this->CA_PATH = $CA_PATH;

        }

		/**
		 * PDO connection
		 */
		 public function PDO()
		 {
		 	if (!$this->PDO_conn){
			 	$dsn = "mysql:host=$this->Host;dbname=$this->Database;charset=utf8";
				$opt = array(
				    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				    PDO::ATTR_EMULATE_PREPARES   => false,
				);
			 	$this->PDO_conn = new PDO($dsn, $this->User, $this->Password, $opt);
				if (!$this->PDO_conn){
					$dsn = "mysql: host=$this->Host2;dbname=$this->Database;charset=utf8";
					$opt = array(
					    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					    PDO::ATTR_EMULATE_PREPARES   => false,
						PDO::MYSQL_ATTR_SSL_CAPATH => $this->CA_PATH,
					);
					$this->PDO_conn = new PDO($dsn, $this->User, $this->Password, $opt);
				}
			}
			return $this->PDO_conn;
		 }

		 

        /**
         * Database Singleton
         *
         * Creates a singleton to be used in any script within any scope
         *
         * @param string $Database name of TAC database to connect to
         * @return object the database singleton
         */
        public static function Summon( $Database=OQ_DB )
        {
            if( !isset( self::$Instance ))
                self::$Instance = new PDO_db( $Database );
				self::$Instance->PDO();

            return self::$Instance;
        }

    }

