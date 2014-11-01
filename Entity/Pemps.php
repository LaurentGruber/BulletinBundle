<?php

namespace Laurent\BulletinBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Pemps
{
    private $pemps;

    private $pemds;

    public function __construct()
    {
        $this->pemps = new ArrayCollection();
        $this->pemds = new ArrayCollection();
    }

    /**
     * @param \Laurent\BulletinBundle\Entity\ArrayCollection $pemps
     */
    public function setPemps($pemps)
    {
        $this->pemps = $pemps;
    }

    /**
     * @return \Laurent\BulletinBundle\Entity\ArrayCollection
     */
    public function getPemps()
    {
        return $this->pemps;
    }

    /**
     * @param \Laurent\BulletinBundle\Entity\ArrayCollection $pemds
     */
    public function setPemds($pemds)
    {
        $this->pemps = $pemds;
    }

    /**
     * @return \Laurent\BulletinBundle\Entity\ArrayCollection
     */
    public function getPemds()
    {
        return $this->pemds;
    }


}