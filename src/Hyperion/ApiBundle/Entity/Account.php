<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 */
class Account implements HyperionEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="account")
     */
    protected $projects;

    /**
     * @ORM\OneToMany(targetEntity="Repository", mappedBy="account")
     */
    protected $repos;

    /**
     * @ORM\OneToMany(targetEntity="Proxy", mappedBy="account")
     */
    protected $proxies;

    /**
     * @ORM\OneToMany(targetEntity="Credential", mappedBy="account")
     */
    protected $credentials;


    // --


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projects    = new ArrayCollection();
        $this->repos       = new ArrayCollection();
        $this->proxies     = new ArrayCollection();
        $this->credentials = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Account
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add projects
     *
     * @param Project $projects
     * @return Account
     */
    public function addProject(Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param Project $projects
     */
    public function removeProject(Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add repos
     *
     * @param Repository $repos
     * @return Account
     */
    public function addRepo(Repository $repos)
    {
        $this->repos[] = $repos;

        return $this;
    }

    /**
     * Remove repos
     *
     * @param Repository $repos
     */
    public function removeRepo(Repository $repos)
    {
        $this->repos->removeElement($repos);
    }

    /**
     * Get repos
     *
     * @return Collection
     */
    public function getRepos()
    {
        return $this->repos;
    }

    /**
     * Add proxies
     *
     * @param Proxy $proxies
     * @return Account
     */
    public function addProxy(Proxy $proxies)
    {
        $this->proxies[] = $proxies;

        return $this;
    }

    /**
     * Remove proxies
     *
     * @param Proxy $proxies
     */
    public function removeProxy(Proxy $proxies)
    {
        $this->proxies->removeElement($proxies);
    }

    /**
     * Get proxies
     *
     * @return Collection
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * Add credentials
     *
     * @param Credential $credentials
     * @return Account
     */
    public function addCredential(Credential $credentials)
    {
        $this->credentials[] = $credentials;

        return $this;
    }

    /**
     * Remove credentials
     *
     * @param Credential $credentials
     */
    public function removeCredential(Credential $credentials)
    {
        $this->credentials->removeElement($credentials);
    }

    /**
     * Get credentials
     *
     * @return Collection
     */
    public function getCredentials()
    {
        return $this->credentials;
    }


    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getName();
    }
}
