<?php
namespace LosWifi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\AbstractOptions;

/**
 * @ORM\MappedSuperclass
 */
class Controller extends AbstractOptions
{

    protected $__strictMode__ = false;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string");
     */
    protected $slug = '';

    /**
     * @ORM\Column(type="string");
     */
    protected $username = '';

    /**
     * @ORM\Column(type="string");
     */
    protected $password = '';

    /**
     * @ORM\Column(type="string");
     */
    protected $baseUrl = 'https://127.0.0.1:8443';

    /**
     * @ORM\Column(type="string",length=10);
     */
    protected $version = '4.0.0';

    /**
     * @ORM\OneToMany(targetEntity="LosWifi\Entity\Site", mappedBy="controller")
     */
    protected $sites;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getSites()
    {
        return $this->sites;
    }

    public function setSites($sites)
    {
        $this->sites = $sites;
        return $this;
    }
}
