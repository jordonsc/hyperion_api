<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="repositories")
 */
class Repository implements HyperionEntityInterface
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
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="repos")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getAccountId")
     */
    protected $account;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="repositories")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getProjectId")
     */
    protected $project;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $private_key;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tag;

    /**
     * @ORM\OneToOne(targetEntity="Proxy")
     */
    protected $proxy;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $host_fingerprint;

    /**
     * @ORM\Column(type="string")
     */
    protected $checkout_directory;

    // --


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
     * Set type
     *
     * @param integer $type
     * @return Repository
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Repository
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Repository
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Repository
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set private_key
     *
     * @param string $privateKey
     * @return Repository
     */
    public function setPrivateKey($privateKey)
    {
        $this->private_key = $privateKey;

        return $this;
    }

    /**
     * Get private_key
     *
     * @return string 
     */
    public function getPrivateKey()
    {
        return $this->private_key;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Repository
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return Repository
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set project
     *
     * @param Project $account
     * @return Repository
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set proxy
     *
     * @param Proxy $proxy
     * @return Repository
     */
    public function setProxy(Proxy $proxy = null)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Get proxy
     *
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Set CheckoutDirectory
     *
     * @param string $checkout_directory
     * @return $this
     */
    public function setCheckoutDirectory($checkout_directory)
    {
        $this->checkout_directory = $checkout_directory;
        return $this;
    }

    /**
     * Get CheckoutDirectory
     *
     * @return string
     */
    public function getCheckoutDirectory()
    {
        return $this->checkout_directory;
    }

    /**
     * Set HostFingerprint
     *
     * @param string $host_fingerprint
     * @return $this
     */
    public function setHostFingerprint($host_fingerprint)
    {
        $this->host_fingerprint = $host_fingerprint;
        return $this;
    }

    /**
     * Get HostFingerprint
     *
     * @return string
     */
    public function getHostFingerprint()
    {
        return $this->host_fingerprint;
    }

    /**
     * Set Name
     *
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    // Serialisers --

    public function getAccountId() {
        return $this->getAccount() ? $this->getAccount()->getId() : null;
    }

    public function getProjectId() {
        return $this->getProject() ? $this->getProject()->getId() : null;
    }

    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getUrl();
    }
}
