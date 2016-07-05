<?php

namespace AppBundle\Controller;

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
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $checklistName = $request->request->get('name');

        // check require parameters 
        if ( ! $checklistName ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for checklist
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        // create new checklist
        $checklist = $this->get('checklist')->createNewChecklist($issue, $checklistName);
        
        // return create new checklist result
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
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $checklistId = $request->request->get('id');
        $newChecklistName = $request->request->get('name');

        // check require parameters 
        if ( ! $checklistId OR ! $newChecklistName ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for checklist
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        // update existing checklist
        $result = $this->get('checklist')->updateChecklist($issue, $checklistId, $newChecklistName);
        
        // return update existing checklist result
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
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $checklistId = $request->request->get('id');

        // check require parameters 
        if ( ! $checklistId ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for checklist
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        // remove existing checklist
        $result = $this->get('checklist')->removeChecklist($issue, $checklistId);
        
        // return remove existing checklist result
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
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $orders = $request->request->get('orders');

        // check require parameters 
        if ( ! $orders OR ! is_array( $orders ) ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for checklist
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        // sort checklists
        $result = $this->get('checklist')->sortChecklists($issue, $orders);
        
        // return sort checklists result
        return new JsonResponse( array('status' => $result ) );
    }
}
