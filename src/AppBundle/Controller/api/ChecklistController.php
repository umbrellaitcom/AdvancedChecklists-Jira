<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChecklistController extends Controller
{
    /**
     * @Route("/{projectKey}/{issueKey}/checklist/create", name="checklists-checklist-create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createChecklistAction(Request $request, $projectKey, $issueKey) 
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }
        
        $issueId = $request->request->get('issue_id');
        $checklistName = $request->request->get('name');
        
        if ( ! $issueId OR ! $checklistName ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        $checklist = $this->get('checklist')->createNewChecklist($issue, $checklistName);
        return new JsonResponse( array('status' => true, 'checklist_id' => $checklist->getId() ) );
    }

    /**
     * @Route("/api/{projectKey}/{issueKey}/checklist/update", name="checklists-checklist-update")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @return JsonResponse
     */
    public function updateChecklistAction(Request $request, $projectKey, $issueKey)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $checklistId = $request->request->get('id');
        $newChecklistName = $request->request->get('name');

        if ( ! $issueId OR ! $checklistId OR ! $newChecklistName ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        $result = $this->get('checklist')->updateChecklist($issue, $checklistId, $newChecklistName);
        return new JsonResponse( array('status' => $result) );
    }
    
    /**
     * @Route("/api/{projectKey}/{issueKey}/checklist/remove", name="checklists-checklist-remove")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeChecklistAction(Request $request, $projectKey, $issueKey)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $checklistId = $request->request->get('id');

        if ( ! $issueId OR ! $checklistId ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        $result = $this->get('checklist')->removeChecklist($issue, $checklistId);
        return new JsonResponse( array('status' => $result ) );
    }

    /**
     * @Route("/api/{projectKey}/{issueKey}/checklist/sortable", name="checklists-checklist-sortable")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sortableUpdateAction(Request $request, $projectKey, $issueKey)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $orders = $request->request->get('orders');

        if ( ! $issueId OR ! $orders OR ! is_array( $orders ) ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        $result = $this->get('checklist')->sortChecklists($issue, $orders);
        return new JsonResponse( array('status' => $result ) );
    }
}
