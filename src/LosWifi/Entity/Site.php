<?php
namespace LosWifi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\AbstractOptions;

/**
 * @ORM\MappedSuperClass
 */
class Site extends AbstractOptions
{

    protected $__strictMode__ = false;

    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=64);
     */
    protected $id;

    /**
     * @ORM\Column(type="string");
     */
    protected $description;

    /**
     * @ORM\Column(type="string",length=64);
     */
    protected $name;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true});
     */
    protected $numAp;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true});
     */
    protected $numSta;

    /**
     * @ORM\Column(type="string",length=64);
     */
    protected $role;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = trim((string) $description);

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    public function getNumAp()
    {
        return $this->numAp;
    }

    public function setNumAp($numAp)
    {
        $this->numAp = (int) $numAp;

        return $this;
    }

    public function getNumSta()
    {
        return $this->numSta;
    }

    public function setNumSta($numSta)
    {
        $this->numSta = (int) $numSta;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = (string) $role;

        return $this;
    }
}
