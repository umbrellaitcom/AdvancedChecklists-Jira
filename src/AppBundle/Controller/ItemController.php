<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ItemController extends Controller
{
    /**
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/item/create", name="checklists-item-create")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @param $issueId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createItemAction(Request $request, $projectKey, $issueKey, $issueId) 
    {
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }
        
        // retrieve POST parameters
        $checklistId = $request->request->get('checklist_id');
        $itemText = $request->request->get('item_text');
        $color = $request->request->get('color');
        
        // check require parameters 
        if ( ! $checklistId OR ! $itemText ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for item
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            // return fail if issue not found
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        // create new item
        $item = $this->get('item')->createNewItem($issue, $checklistId, $itemText, $color);
        
        // return create item result
        return new JsonResponse( array('status' => (bool)$item, 'item_id' => $item ? $item->getId() : 0 ) );
    }

    /**
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/item/update", name="checklists-item-update")
     * @param Request $request
     * @param $projectKey
     * @param $issueKey
     * @param $issueId
     * @return JsonResponse
     */
    public function updateItemAction(Request $request, $projectKey, $issueKey, $issueId)
    {
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $checklistId = $request->request->get('checklist_id');
        $itemId = $request->request->get('item_id');
        $newItemText = $request->request->get('item_text');
        $color = $request->request->get('color');

        // check require parameters 
        if ( ! $checklistId OR ! $itemId OR ! $newItemText ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for item
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        // update existing item
        $result = $this->get('item')->updateItem($issue, $checklistId, $itemId, $newItemText, $color);
        
        // return update item result
        return new JsonResponse( array('status' => $result) );
    }
    
    /**
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/item/remove", name="checklists-item-remove")
     * @param Request $request
     * @param Request $projectKey
     * @param Request $issueKey
     * @param Request $issueId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeItemAction(Request $request, $projectKey, $issueKey, $issueId)
    {
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $checklistId = $request->request->get('checklist_id');
        $itemId = $request->request->get('item_id');

        // check require parameters 
        if ( ! $checklistId OR ! $itemId ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for item
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }
        
        // remove existing item
        $result = $this->get('item')->removeItem($issue, $checklistId, $itemId);

        // return remove item result
        return new JsonResponse( array('status' => $result ) );
    }

    /**
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/item/complete", name="checklists-item-complete")
     * @param Request $request
     * @param Request $projectKey
     * @param Request $issueKey
     * @param Request $issueId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function completeItemAction(Request $request, $projectKey, $issueKey, $issueId) 
    {
        // available only POST requests for API
        if ( $request->getMethod() != 'POST') {
            return new JsonResponse(array('status' => false, 'message' => 'available only POST request'));
        }

        // retrieve POST parameters
        $checklistId = $request->request->get('checklist_id');
        $itemId = $request->request->get('item_id');

        // check require parameters 
        if ( ! $checklistId OR ! $itemId ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for item
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        // complete item
        $item = $this->get('item')->completeItem($issue, $checklistId, $itemId);
        
        // return false status on fail action
        if ( ! $item ) {
            return new JsonResponse( array('status' => false ) );
        }

        // return complete item result
        return new JsonResponse( array('status' => true, 'checked' => $item->getChecked() ) );
    }

    /**
     * @Route("/api/{projectKey}/{issueKey}/{issueId}/item/sortable", name="checklists-item-sortable")
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
        $orders = $request->request->get('checklists_sort');

        // check require parameters 
        if ( ! $orders OR ! is_array( $orders ) ) {
            return new JsonResponse(array('status' => false, 'message' => 'needed data is lost'));
        }

        // find issue for item
        $issue = $this->getDoctrine()->getRepository('AppBundle:Issue')->findOneIssue($projectKey, $issueId, $issueKey);
        if ( ! $issue ) {
            return new JsonResponse(array('status' => false, 'message' => 'Issue is not found'));
        }

        // sort items
        $result = $this->get('item')->sortItems($issue, $orders);
        
        // return sort item result
        return new JsonResponse( array('status' => $result ) );
    }
}
