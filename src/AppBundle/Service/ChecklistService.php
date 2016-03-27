<?php

namespace AppBundle\Service;

use AppBundle\Entity\Issue;
use AppBundle\Entity\Checklist;
use Doctrine\ORM\EntityManager;

/**
 * Class ChecklistService
 * @package AppBundle\Service
 */
class ChecklistService 
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ChecklistService constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager) 
    {
        $this->em = $manager;   
    }

    /**
     * Retrieve all checklists for issue
     * @param Issue $issue
     * @return array
     */
    public function getChecklistsForIssue(Issue $issue) 
    {
        $checklistsData = array();
        foreach ( $issue->getChecklists() as $checklist ) {
            $clData = new \stdClass();
            $clData->id = $checklist->getId();
            $clData->name = $checklist->getName();

            $clData->items = array();
            foreach ( $checklist->getItems() as $item ) {
                $itData = new \stdClass();
                $itData->id = $item->getId();
                $itData->text = $item->getText();
                $itData->checked = $item->getChecked();
                $clData->items[] = $itData;
            }
            
            $checklistsData[] = $clData;
        }
        
        return $checklistsData;
    }

    /**
     * Create new checklist in issue
     * @param Issue $issue
     * @param $checklistName
     * @return Checklist
     */
    public function createNewChecklist(Issue $issue, $checklistName) 
    {
        $newChecklist = new Checklist();
        $newChecklist->setIssue( $issue );
        $newChecklist->setName( $checklistName );

        // detect sort
        $sort = 0;
        foreach( $issue->getChecklists() as $checklist )
        {
            if ( $checklist->getSort() > $sort )
            {
                $sort = $checklist->getSort();
            }
        }
        $newChecklist->setSort($sort);
        
        $this->em->persist( $newChecklist );
        $this->em->flush();
        
        return $newChecklist;
    }

    /**
     * Update checklist
     * @param Issue $issue
     * @param $checklistId
     * @param $newChecklistName
     * @return bool
     */
    public function updateChecklist(Issue $issue, $checklistId, $newChecklistName)
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

        $checklistToUpdate->setName($newChecklistName);
        $this->em->flush();
        
        return true;
    }

    /**
     * Remove checklist from issue
     * @param Issue $issue
     * @param $checklistId
     * @return bool
     */
    public function removeChecklist(Issue $issue, $checklistId) 
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
        
        foreach ( $checklistToRemove->getItems() as $item ) {
            $this->em->remove( $item );
        }
        $this->em->remove($checklistToRemove);
        $this->em->flush();
        
        return true;
    }

    /**
     * Sort checklists in issue
     * @param $issue
     * @param $orders
     * @return bool
     */
    public function sortChecklists($issue, $orders) 
    {
        $orders = array_values($orders);
        foreach ( $orders as $key => $checklist_id ) {
            $checklist = $this->em->getRepository('AppBundle:Checklist')->findByIssueAndId( $issue, $checklist_id );
            if ( ! $checklist ) {
                return false;
            }
            $checklist->setSort($key);
        }
        $this->em->flush();
        
        return true;
    }
}