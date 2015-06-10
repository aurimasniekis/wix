<?php

namespace Wix\TreeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Wix\TreeBundle\Entity\ItemRepository")
 */
class Item
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="Wix\TreeBundle\Entity\Item", inversedBy="children")
     */
    private $parent;

    /**
     * @var Item[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Wix\TreeBundle\Entity\Item", mappedBy="parent")
     */
    private $children;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->level = 0;
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
     * @return Item
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
     * @return Item
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Item $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        $this->calculateLevel($parent);

        return $this;
    }

    /**
     * @return ArrayCollection|Item[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Item $item
     *
     * @return $this
     */
    public function addChildren(Item $item)
    {
        $this->children->add($item);

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    protected function calculateLevel(Item $parent = null)
    {
        $level = 0;

        if ($parent) {
            $level++;
        }

        while (null !== ($parent = $parent->getParent())) {
            $level++;
        }

        $this->setLevel($level);
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return str_repeat('--', $this->getLevel()) . ' ' . $this->getName();
    }
}
