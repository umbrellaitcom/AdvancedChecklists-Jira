<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ViewController extends Controller
{
    /**
     * @Route("/project/{projectKey}/issue/{issueKey}/tab", name="checklists-tab")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabAction(Request $request, $projectKey, $issueKey) 
    {
        /**
         * Retrieve Issue
         */
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($this->getUser()->getClientKey(), $projectKey, $request->query->get('issue_id'), $issueKey);
        if ( ! $issue ) {
            $issue = $this->get('issue')->createIssue($projectKey, $issueKey, $request, $this->getUser());
        }

        /**
         * Render template
         */
        return $this->render('AppBundle::tab.html.twig', array(
            'checklistsData' => $this->get('checklist')->getChecklistsForIssue( $issue ),
            'issueId' => $request->query->get('issue_id'),
            'issueKey' => $issueKey,
            'projectKey' => $projectKey,
            'baseUrl' => $this->getUser()->getBaseUrl(),
        ));
    }

    /**
     * @Route("/", name="promo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function promoAction(Request $request)
    {
        /**
         * Render template
         */
        return $this->render('AppBundle::promo.html.twig', array());
    }
}
