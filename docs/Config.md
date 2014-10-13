# Using FOSUserBundle for login

# 1. create fos_user table based on Hyperion\ApiBundle\Entity\User.php
app/console doctrine:schema:update --force

# 2. create superadmin 'admin' user through console, will prompt for more info
app/console fos:user:create admin --super-admin

# 3 (optional) create generic user through console, will prompt for more info
app/console fos:user:create userName

# 4 guide to create|modify|delete users through command line
https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/command_line_tools.md

#################################################

# Using PDO to save session in database
# query for creating where to store the session

CREATE TABLE `session` (
    `session_id` varchar(255) NOT NULL,
    `session_value` text NOT NULL,
    `session_time` int(11) NOT NULL,
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

