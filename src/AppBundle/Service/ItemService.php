<?php

namespace AppBundle\Service;

use AppBundle\Entity\Issue;
use AppBundle\Entity\Checklist;
use AppBundle\Entity\Item;
use Doctrine\ORM\EntityManager;

/**
 * Class ItemService
 * @package AppBundle\Service
 */
class ItemService 
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ItemService constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager) 
    {
        $this->em = $manager;   
    }

    /**
     * Create new item in checklist
     * @param Issue $issue
     * @param $checklistId
     * @param $itemText
     * @param $color
     * @return Item|bool
     */
    public function createNewItem(Issue $issue, $checklistId, $itemText, $color) 
    {
        $checklistWithNewItem = false;
        foreach ( $issue->getChecklists() as $checklist ) {
            if ($checklist->getId() == $checklistId) {
                $checklistWithNewItem = $checklist;
            }
        }

        if( ! $checklistWithNewItem ) {
            return false;
        }
            
        $newItem = new Item();
        $newItem->setChecked( false );
        $newItem->setChecklist( $checklistWithNewItem );
        $newItem->setText( $itemText );
        $newItem->setColor( $color );
        
        // detect sort
        $sort = 0;
        foreach( $checklistWithNewItem->getItems() as $item ) 
        {
            if ( $item->getSort() > $sort ) 
            {
                $sort = $item->getSort();
            }
        }
        $newItem->setSort($sort);
        
        $this->em->persist( $newItem );
        $this->em->flush();
        
        return $newItem;
    }

    /**
     * Update item text in checklist
     * @param Issue $issue
     * @param $checklistId
     * @param $itemId
     * @param $newItemText
     * @return bool
     */
    public function updateItem(Issue $issue, $checklistId, $itemId, $newItemText, $color)
    {
        $checklistToUpdate = false;
        foreach ( $issue->getChecklists() as $checklist ) {
            if ($checklist->getId() == $checklistId) {
                $checklistToUpdate = $checklist;
            }
        }

        if( ! $checklistToUpdate ) {
            return false;
        }

        $itemToUpdate = false;
        foreach ( $checklistToUpdate->getItems() as $item) {
            if ($item->getId() == $itemId) {
                $itemToUpdate = $item;
            }
        }

        if( ! $itemToUpdate ) {
            return false;
        }

        $itemToUpdate->setText($newItemText);
        $itemToUpdate->setColor($color);
        
        $this->em->flush();
        
        return true;
    }

    /**
     * Remove item from checklist
     * @param Issue $issue
     * @param $checklistId
     * @param $itemId
     * @return bool
     */
    public function removeItem(Issue $issue, $checklistId, $itemId) 
    {
        $checklistToRemove = false;
        foreach ( $issue->getChecklists() as $checklist ) {
            if ($checklist->getId() == $checklistId) {
                $checklistToRemove = $checklist;
            }
        }
        
        if( ! $checklistToRemove ) {
            return false;
        }
        
        $itemToRemove = false;
        foreach ( $checklistToRemove->getItems() as $item ) {
            if ($item->getId() == $itemId) {
                $itemToRemove = $item;
            }
        }

        if( ! $itemToRemove ) {
            return false;
        }
        
        $this->em->remove($itemToRemove);
        $this->em->flush();
        
        return true;
    }

    /**
     * Complete / Un-complete (toggle) item in checklist 
     * @param Issue $issue
     * @param $checklistId
     * @param $itemId
     * @return bool
     */
    public function completeItem(Issue $issue, $checklistId, $itemId)
    {
        $checklistWithItem = false;
        foreach ( $issue->getChecklists() as $checklist ) {
            if ($checklist->getId() == $checklistId) {
                $checklistWithItem = $checklist;
            }
        }

        if( ! $checklistWithItem ) {
            return false;
        }

        $itemToComplete = false;
        foreach ( $checklistWithItem->getItems() as $item ) {
            if ($item->getId() == $itemId) {
                $itemToComplete = $item;
            }
        }

        if( ! $itemToComplete ) {
            return false;
        }

        $itemToComplete->setChecked( ! $itemToComplete->getChecked() );
        $this->em->flush();

        return $itemToComplete;
    }

    /**
     * Change sort for items
     * @param $issue
     * @param $orders
     * @return bool
     */
    public function sortItems($issue, $orders) 
    {
        foreach ( $orders as $checklistId => $itemIds ) {
            $checklist = $this->em->getRepository('AppBundle:Checklist')->findByIssueAndId( $issue, $checklistId );
            if ( ! $checklist ) {
                return false;
            }
            
            foreach ($itemIds as $key => $itemId) {
                $item = $this->em->getRepository('AppBundle:Item')->find( $itemId );
                if ( ! $item ) {
                    return false;
                }
    
                $item->setChecklist( $checklist );
                $item->setSort($key);
            }

        }
        $this->em->flush();

        return true;
    }
}