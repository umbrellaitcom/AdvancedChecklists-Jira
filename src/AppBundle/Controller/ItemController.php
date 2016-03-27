<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ItemController extends Controller
{
    /**
     * @Route("/project/{projectKey}/issue/{issueKey}/item/create", name="checklists-item-create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createItemAction(Request $request, $projectKey, $issueKey) 
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }
        
        $issueId = $request->request->get('issue_id');
        $checklistId = $request->request->get('checklist_id');
        $itemText = $request->request->get('item_text');
        
        if ( ! $issueId OR ! $checklistId OR ! $itemText ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($this->getUser()->getClientKey(), $projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        $item = $this->get('item')->createNewItem($issue, $checklistId, $itemText);
        return new JsonResponse( array('status' => (bool)$item, 'item_id' => $item ? $item->getId() : 0 ) );
    }

    /**
     * @Route("/project/{projectKey}/issue/{issueKey}/item/update", name="checklists-item-update")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @return JsonResponse
     */
    public function updateItemAction(Request $request, $projectKey, $issueKey)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $checklistId = $request->request->get('checklist_id');
        $itemId = $request->request->get('item_id');
        $newItemText = $request->request->get('item_text');

        if ( ! $issueId OR ! $checklistId OR ! $itemId OR ! $newItemText ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($this->getUser()->getClientKey(), $projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        $result = $this->get('item')->updateItem($issue, $checklistId, $itemId, $newItemText);
        return new JsonResponse( array('status' => $result) );
    }
    
    /**
     * @Route("/project/{projectKey}/issue/{issueKey}/item/remove", name="checklists-item-remove")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeItemAction(Request $request, $projectKey, $issueKey)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $checklistId = $request->request->get('checklist_id');
        $itemId = $request->request->get('item_id');
        
        if ( ! $issueId OR ! $checklistId OR ! $itemId ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($this->getUser()->getClientKey(), $projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        $result = $this->get('item')->removeItem($issue, $checklistId, $itemId);
        return new JsonResponse( array('status' => $result ) );
    }

    /**
     * @Route("/project/{projectKey}/issue/{issueKey}/item/complete", name="checklists-item-complete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function completeItemAction(Request $request, $projectKey, $issueKey) 
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $checklistId = $request->request->get('checklist_id');
        $itemId = $request->request->get('item_id');

        if ( ! $issueId OR ! $checklistId OR ! $itemId ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($this->getUser()->getClientKey(), $projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        $item = $this->get('item')->completeItem($issue, $checklistId, $itemId);
        
        if ( ! $item ) {
            return new JsonResponse( array('status' => false ) );
        }
        
        return new JsonResponse( array('status' => true, 'checked' => $item->getChecked() ) );
    }

    /**
     * @Route("/project/{projectKey}/issue/{issueKey}/item/sortable", name="checklists-item-sortable")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sortableUpdateAction(Request $request, $projectKey, $issueKey)
    {
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        $issueId = $request->request->get('issue_id');
        $orders = $request->request->get('checklists_sort');

        if ( ! $issueId OR ! $orders OR ! is_array( $orders ) ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($this->getUser()->getClientKey(), $projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        $result = $this->get('item')->sortItems($issue, $orders);
        return new JsonResponse( array('status' => $result ) );
    }
}
