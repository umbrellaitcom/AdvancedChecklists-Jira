<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppController extends Controller
{
    /**
     * @Route("/{projectKey}/{issueKey}/{issueId}/", name="checklists-tab")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @param $issueId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function appAction(Request $request, $projectKey, $issueKey, $issueId) 
    {
        // Retrieve Issue
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            // If Issue not exist we should create new issue
            $issue = $this->get('issue')->createIssue($projectKey, $issueKey, $issueId, $this->getUser());
        }

        // Render template
        return $this->render('AppBundle::app.html.twig', array(
            'checklistsData' => $this->get('checklist')->getChecklistsForIssue( $issue ),
            'issueId' => $issueId,
            'issueKey' => $issueKey,
            'projectKey' => $projectKey,
            'baseUrl' => $this->getUser()->getBaseUrl(),
        ));
    }
}
