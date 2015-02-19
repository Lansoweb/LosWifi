<?php
namespace LosWifi\Entity;

use Zend\Stdlib\AbstractOptions;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperClass
 */
class Device extends AbstractOptions
{

    protected $__strictMode__ = false;

    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=64)
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=15)
     */
    protected $ip;

    /**
     * @ORM\Column(type="string",length=20)
     */
    protected $mac;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true}, name="rx_bytes")
     */
    protected $rxBytes;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true}, name="tx_bytes")
     */
    protected $txBytes;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $state = false;

    /**
     * @ORM\ManyToOne(targetEntity="LosWifi\Entity\Site", inversedBy="devices")
     * @ORM\JoinColumn(nullable=true, onDelete="RESTRICT")
     */
    protected $site;

    private $statusChange = 0;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    public function getMac()
    {
        return $this->mac;
    }

    public function setMac($mac)
    {
        $this->mac = strtoupper($mac);

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getRxBytes()
    {
        return $this->rxBytes;
    }

    public function setRxBytes($rx)
    {
        $this->rxBytes = $rx;

        return $this;
    }

    public function getTxBytes()
    {
        return $this->txBytes;
    }

    public function setTxBytes($tx)
    {
        $this->txBytes = $tx;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    public function wentOffline()
    {
        return $this->statusChange === -1;
    }

    public function wentOnline()
    {
        return $this->statusChange === 1;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $state = (bool) $state;
        if ($this->state === $state) {
            $this->statusChange = 0; // No change
        } elseif ($this->state === true && $state === false) {
            $this->statusChange = -1; // went offline
        } elseif (($this->state === false || $this->state === null) && $state === true) {
            $this->statusChange = 1; // went online
        }
        $this->state = $state;

        return $this;
    }
}
