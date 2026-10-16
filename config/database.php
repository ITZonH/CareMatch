<?php
	//Name: Hamed Zon
	//ID: C00259228
	//Date: 16/10/2026
    //load database credentials from private file
    $config =  require dirname(__DIR__, 2)
    .'/carematch_secrets/database_secrets.php';

    //database connection settings
    $host = $config['host'];
    $port = $config['port'];
    $dbname = $config['dbname'];
    $username = $config['username'];
    $password = $config['password'];

    //creating the connection string
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    try{
        // connecting to mysql using pdo
        $pdo = new PDO($dsn,$username,$password);

        //configure the pdo

        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
    }

    catch(PDOEXCEPTION $e){
        error_log($e->getMessage());
        http_response_code(500);
        exit("Database connection failed.");
    }
