Using FOSUserBundle
===================

### Creating a Super-Admin user
`app/console fos:user:create admin --super-admin`

### Creating generic users
`app/console fos:user:create userName`

### More Info

* [FOS User CLI](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/command_line_tools.md)


PDO Sessions
============
For PDO sessions you must manually add a table to the database, Doctrine will not do this for you -
    
    CREATE TABLE `session` (
        `session_id` varchar(255) NOT NULL,
        `session_value` text NOT NULL,
        `session_time` int(11) NOT NULL,
        PRIMARY KEY (`session_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
