<?php

namespace Wix\TreeBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Wix\TreeBundle\Controller\DefaultController;
use Wix\TreeBundle\Entity\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testLevelCalculation()
    {
        $item = new Item();
        $item->setName('Root');

        $this->assertEquals($item->getLevel(), 0);

        $subItem = new Item();
        $subItem->setName('Sub Item');

        $subItem->setParent($item);

        $this->assertEquals($subItem->getLevel(), 1);

        $subSubItem = new Item();
        $subSubItem->setName('Sub Sub Item');

        $subSubItem->setParent($subItem);

        $this->assertEquals($subSubItem->getLevel(), 2);
    }

    public function testTree()
    {
        $item = new Item();
        $item->setId(1);
        $item->setName('Root');

        $subItem = new Item();
        $subItem->setId(2);
        $subItem->setName('Sub Item');
        $subItem->setParent($item);

        $subSubItem = new Item();
        $subSubItem->setId(3);
        $subSubItem->setName('Sub Sub Item');
        $subSubItem->setParent($subItem);

        $item->addChildren($subItem);
        $subItem->addChildren($subSubItem);

        $items          = new ArrayCollection([$item, $subItem, $subSubItem]);
        $itemsRecursive = new ArrayCollection([$item]);

        $expected = [
            1 => [
                'name'      => 'Root',
                'parent_id' => null,
                'children'  => [
                    2 => [
                        'name'      => 'Sub Item',
                        'parent_id' => 1,
                        'children'  => [
                            3 => [
                                'name'      => 'Sub Sub Item',
                                'parent_id' => 2,
                                'children'  => [
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $controller = new DefaultController();

        $this->assertEquals($expected, $controller->itemsTree($items));

        $this->assertEquals($expected, $controller->itemsTreeRecursion($itemsRecursive));
    }
}
