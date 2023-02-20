<?

class Database {

    private string $username;
    private string $password;
    private string $hostname;
    private string $port;
    private string $dbname;
    private PDO $connection;

    public function __construct(string $username, string $password, string $hostname, string $port, string $dbname) {

        $this->username = $username;
        $this->password = $password;
        $this->hostname = $hostname;
        $this->port = $port;
        $this->dbname = $dbname;

    }

    public function connect(): void {

        if(isset($this->connection)) return;

        try {

            $this->connection = new PDO(

                "pgsql:host=$this->hostname;port=$this->port;dbname=$this->dbname",
                $this->username,
                $this->password,
                ["sslmode" => "prefer"]

            );

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        }catch(PDOException $e) {

            die("Connection failed: {$e->getMessage()}");

        }

    }

    public function getConnection(): PDO {

        return $this->connection;

    }

}