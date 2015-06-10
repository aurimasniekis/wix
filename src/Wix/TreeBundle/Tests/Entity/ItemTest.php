<?php

namespace Wix\TreeBundle\Tests\Entity;

use Wix\TreeBundle\Entity\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testLevelCalculation()
    {
        $item = new Item();
        $item->setName('Root');

        $this->assertEquals($item->getLevel(), 0);

        $subItem = new Item();
        $item->setName('Sub Item');

        $subItem->setParent($item);

        $this->assertEquals($subItem->getLevel(), 1);

        $subSubItem = new Item();
        $subSubItem->setName('Sub Sub Item');

        $subSubItem->setParent($subItem);

        $this->assertEquals($subSubItem->getLevel(), 2);
    }

}
