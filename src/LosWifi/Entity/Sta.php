<?php
namespace LosWifi\Entity;

use Zend\Stdlib\AbstractOptions;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class Sta extends AbstractOptions
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
     * @ORM\Column(type="string",length=20, name="ap_mac")
     */
    protected $apMac;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $hostname;

    /**
     * @ORM\Column(type="string", length=64, nullable=true, name="site_id")
     */
    protected $siteId;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true}, name="first_seen")
     */
    protected $firstSeen;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true}, name="last_seen")
     */
    protected $lastSeen;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true})
     */
    protected $uptime;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $signal;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true}, name="rx_bytes")
     */
    protected $rxBytes;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true}, name="tx_bytes")
     */
    protected $txBytes;

    /**
     * @ORM\ManyToOne(targetEntity="LosWifi\Entity\Site", inversedBy="devices")
     * @ORM\JoinColumn(nullable=true, onDelete="RESTRICT")
     */
    protected $site;

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

    public function getApMac()
    {
        return $this->apMac;
    }

    public function setApMac($apMac)
    {
        $this->apMac = strtoupper($apMac);

        return $this;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getSiteId()
    {
        return $this->siteId;
    }

    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function getFirstSeen()
    {
        return $this->firstSeen;
    }

    public function setFirstSeen($firstSeen)
    {
        $this->firstSeen = $firstSeen;

        return $this;
    }

    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getUptime()
    {
        return $this->uptime;
    }

    public function setUptime($uptime)
    {
        $this->uptime = $uptime;

        return $this;
    }

    public function getSignal()
    {
        return $this->signal;
    }

    public function setSignal($signal)
    {
        $this->signal = $signal;

        return $this;
    }

    public function getRxBytes()
    {
        return $this->rxBytes;
    }

    public function setRxBytes($rxBytes)
    {
        $this->rxBytes = $rxBytes;

        return $this;
    }

    public function getTxBytes()
    {
        return $this->txBytes;
    }

    public function setTxBytes($txBytes)
    {
        $this->txBytes = $txBytes;

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
}
