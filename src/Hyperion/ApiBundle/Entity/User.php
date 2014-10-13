<?php

namespace Hyperion\ApiBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * 1. create fos_user table based on this class
 * app/console doctrine:schema:update --force
 *
 * 2. create superadmin 'admin' user, will prompt for more info
 * app/console fos:user:create admin --super-admin
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
}