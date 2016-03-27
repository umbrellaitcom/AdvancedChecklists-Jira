<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueRepository")
 */
class Issue
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="issueId", type="integer")
     */
    private $issueId;

    /**
     * @var string
     *
     * @ORM\Column(name="issueKey", type="string", length=100)
     */
    private $issueKey;

    /**
     * @var string
     *
     * @ORM\Column(name="projectKey", type="string", length=100)
     */
    private $projectKey;

    /**
     * @ORM\OneToMany(targetEntity="Checklist", mappedBy="issue")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $checklists;

    /**
     * @var string
     *
     * @ORM\Column(name="client_key", type="string", length=255)
     */
    private $clientKey;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set issueId
     *
     * @param integer $issueId
     *
     * @return Issue
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId
     *
     * @return int
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set issueKey
     *
     * @param string $issueKey
     *
     * @return Issue
     */
    public function setIssueKey($issueKey)
    {
        $this->issueKey = $issueKey;

        return $this;
    }

    /**
     * Get issueKey
     *
     * @return string
     */
    public function getIssueKey()
    {
        return $this->issueKey;
    }

    /**
     * Set projectKey
     *
     * @param string $projectKey
     *
     * @return Issue
     */
    public function setProjectKey($projectKey)
    {
        $this->projectKey = $projectKey;

        return $this;
    }

    /**
     * Get projectKey
     *
     * @return string
     */
    public function getProjectKey()
    {
        return $this->projectKey;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->checklists = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add checklist
     *
     * @param \AppBundle\Entity\Checklist $checklist
     *
     * @return Issue
     */
    public function addChecklist(\AppBundle\Entity\Checklist $checklist)
    {
        $this->checklists[] = $checklist;

        return $this;
    }

    /**
     * Remove checklist
     *
     * @param \AppBundle\Entity\Checklist $checklist
     */
    public function removeChecklist(\AppBundle\Entity\Checklist $checklist)
    {
        $this->checklists->removeElement($checklist);
    }

    /**
     * Get checklists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChecklists()
    {
        return $this->checklists;
    }

    /**
     * Set clientKey
     *
     * @param string $clientKey
     *
     * @return Issue
     */
    public function setClientKey($clientKey)
    {
        $this->clientKey = $clientKey;

        return $this;
    }

    /**
     * Get clientKey
     *
     * @return string
     */
    public function getClientKey()
    {
        return $this->clientKey;
    }
}
