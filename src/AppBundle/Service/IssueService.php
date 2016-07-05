<?php

namespace AppBundle\Service;

use AppBundle\Entity\Issue;
use Doctrine\ORM\EntityManager;

/**
 * Class IssueService
 * @package AppBundle\Service
 */
class IssueService 
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * IssueService constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager) 
    {
        $this->em = $manager;   
    }

    /**
     * @param $projectKey
     * @param $issueKey
     * @param $issueId
     * @param $user
     * @return Issue
     */
    public function createIssue($projectKey, $issueKey, $issueId, $user) {
        $issue = new Issue();
        $issue->setProjectKey($projectKey);
        $issue->setIssueId($issueId);
        $issue->setIssueKey($issueKey);
        $issue->setClientKey($user->getClientKey());
        
        $this->em->persist($issue);
        $this->em->flush();   
        
        return $issue;
    }
}