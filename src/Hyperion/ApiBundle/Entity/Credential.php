<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hyperion\Dbal\Enum\Provider;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="credentials")
 */
class Credential implements HyperionEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="credentials")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getAccountId")
     */
    protected $account;

    /**
     * @ORM\Column(type="integer")
     */
    protected $provider;

    /**
     * @ORM\Column(type="string")
     */
    protected $access_key;

    /**
     * @ORM\Column(type="string")
     */
    protected $secret;

    /**
     * @ORM\Column(type="string")
     */
    protected $region;

    /**
     * @ORM\OneToMany(targetEntity="Environment", mappedBy="credential")
     */
    protected $environments;

    // --


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->environments = new ArrayCollection();
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
     * Set provider
     *
     * @param integer $provider
     * @return Credential
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return integer
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set access_key
     *
     * @param string $key
     * @return Credential
     */
    public function setAccessKey($key)
    {
        $this->access_key = $key;

        return $this;
    }

    /**
     * Get access_key
     *
     * @return string
     */
    public function getAccessKey()
    {
        return $this->access_key;
    }

    /**
     * Set secret
     *
     * @param string $secret
     * @return Credential
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set region
     *
     * @param string $region
     * @return Credential
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return Credential
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
     * Add environments
     *
     * @param Environment $environments
     * @return Credential
     */
    public function addEnvironment(Environment $environments)
    {
        $this->environments[] = $environments;

        return $this;
    }

    /**
     * Remove environments
     *
     * @param Environment $environments
     */
    public function removeEnvironment(Environment $environments)
    {
        $this->environments->removeElement($environments);
    }

    /**
     * Get environments
     *
     * @return Collection
     */
    public function getEnvironments()
    {
        return $this->environments;
    }


    // Serialisers --

    public function getAccountId() {
        return $this->getAccount() ? $this->getAccount()->getId() : null;
    }

    public function __toString()
    {
        return '['.$this->getId().'] '.Provider::memberByValue($this->getProvider())->key().' - '.$this->getRegion();
    }

}
