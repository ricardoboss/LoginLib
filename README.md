# LoginLib

\- **LoginLib** is a php library that provides the background mechanics for registering new users and logging them in.

### Navigation:
1. [Installation](#installation)
2. [Database](#database)
3. [Methods](#methods)

## Installation <small>[top](#loginlib)</small>

The installation is easy, the implementation can be difficult:

1.  Download `LoginLib.php` from the `dist` directory in your desired release branch.
2.  Wherever your need to check if a user is logged in or plan to use one of the functions of LoginLib, `include('LoginLib.php')`.
3.  Create the LoginLib instance using your config. An example `config.php` can be found [here](https://github.com/MCMainiac/LoginLib/blob/master/test/config.php).
	* Look [here](https://github.com/MCMainiac/LoginLib/blob/master/test/load.php) to see how to implement the config.
4. Create an instance of your `IDatabase` implementation and pass it as the second parameter in the constructor. More on that topic [later on](#database).
5. Use [the official API](https://mcmainiac.github.io/LoginLib/namespaces/LoginLib.html) to get the information you want from LoginLib.

**Don't forget to `use LoginLib;` or create the instance like this: `$loginlib = new LoginLib\LoginLib($config, $db);`

## Database <small>[top](#loginlib)</small>

To communicate with your database, LoginLib uses the `IDatabase` interface.

To create your own implementation, I made [an example](https://github.com/MCMainiac/LoginLib/blob/master/test/DatabaseAdapter.php) `DatabaseAdapter` that is using the `MysqliDb` class from [here](https://github.com/joshcam/PHP-MySQLi-Database-Class).

## Methods <small>[top](#loginlib)</small>

Definitions of the methods can be found in the [API Documentation](https://mcmainiac.github.io/LoginLib/classes/LoginLib.LoginLib.html).
