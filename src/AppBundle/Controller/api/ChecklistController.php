<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChecklistController extends Controller
{
    /**
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/checklist/create", name="checklists-checklist-create")
     * @param Request $request
     * @param Request $projectKey
     * @param Request $issueKey
     * @param Request $issueId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createChecklistAction(Request $request, $projectKey, $issueKey, $issueId) 
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }
        
        $checklistName = $request->request->get('name');
        
        if ( ! $checklistName ) {
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
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/checklist/update", name="checklists-checklist-update")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @param $issueId
     * @return JsonResponse
     */
    public function updateChecklistAction(Request $request, $projectKey, $issueKey, $issueId)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $checklistId = $request->request->get('id');
        $newChecklistName = $request->request->get('name');

        if ( ! $checklistId OR ! $newChecklistName ) {
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
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/checklist/remove", name="checklists-checklist-remove")
     * @param Request $request
     * @param Request $projectKey
     * @param Request $issueKey
     * @param Request $issueId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeChecklistAction(Request $request, $projectKey, $issueKey, $issueId)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $checklistId = $request->request->get('id');

        if ( ! $checklistId ) {
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
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/checklist/sortable", name="checklists-checklist-sortable")
     * @param Request $request
     * @param Request $projectKey
     * @param Request $issueKey
     * @param Request $issueId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sortableUpdateAction(Request $request, $projectKey, $issueKey, $issueId)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }
        
        $orders = $request->request->get('orders');

        if ( ! $orders OR ! is_array( $orders ) ) {
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
