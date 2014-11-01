<?php

namespace Laurent\BulletinBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Pemps
{
    private $pemps;

    public function __construct()
    {
        $this->pemps = new ArrayCollection();
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


}