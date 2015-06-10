<?php

namespace Wix\TreeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wix\TreeBundle\Entity\Item;
use Wix\TreeBundle\Form\ItemType;

/**
 * Class DefaultController
 * @package Wix\TreeBundle\Controller
 * @author Aurimas Niekis <aurimas.niekis@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('WixTreeBundle:Item');

        $items = $this->itemsTree($repo->findAll());

        $recursiveItems = $this->itemsTreeRecursion($repo->findBy(['level' => 0]));

        return $this->render(
            'WixTreeBundle:Default:index.html.twig',
            [
                'items' => $items,
                'recursiveItems' => $recursiveItems
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $entity = new Item();
        $form   = $this->createCreateForm($entity);

        return $this->render(
            'WixTreeBundle:Default:new.html.twig',
            [
                'entity' => $entity,
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $entity = new Item();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wix_tree_homepage'));
        }

        return $this->render(
            'WixTreeBundle:Default:new.html.twig',
            [
                'entity' => $entity,
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCreateForm(Item $entity)
    {
        $form = $this->createForm(new ItemType(), $entity, [
            'action' => $this->generateUrl('wix_tree_create'),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * @param Item[]|ArrayCollection $items
     * @param int $parentId
     *
     * @return array
     *
     */
    protected function itemsTree($items, $parentId = NULL)
    {
        $keys = [];
        $values = [];
        foreach ($items as $item) {
            $keys[] = $item->getId();
            $value = [];
            $value['name'] = $item->getName();
            $value['parent_id'] = $item->getParent() ? $item->getParent()->getId() : NULL;
            $value['children'] = [];

            $values[] = $value;
        }

        $array = array_combine($keys, $values);

        foreach ($array as $key => &$value) {
            if (isset($array[$value['parent_id']])) {
                $array[$value['parent_id']]['children'][$key] = &$value;
            }

            unset($value);
        }

        return array_filter($array, function($value) use ($parentId) {
            return $value['parent_id'] == $parentId;
        });
    }

    /**
     * @param Item[]|ArrayCollection $items
     *
     * @return array
     *
     */
    protected function itemsTreeRecursion($items)
    {
        $array = [];

        foreach ($items as $item) {
            $value = [];
            $value['name'] = $item->getName();
            $value['parent_id'] = $item->getParent() ? $item->getParent()->getId() : NULL;
            $value['children'] = $this->itemsTreeRecursion($item->getChildren());

            $array[$item->getId()] = $value;
        }

        return $array;
    }
}
