<?php
/**
 * HistoryController is responsible for processing the incoming requests from the web browser.
 * Provide list of user's search history order of most recent first.
 *
 */
class HistoryController extends \Phalcon\Mvc\Controller
{

    /**
     * This action is responsible for first history page.
     *
     * URL: /history
     */
    public function indexAction()
    {
        $this->cookiesLibrary->setCookie($this->cookies);
        $histories = $this->cookiesLibrary->get();
        $this->view->title = 'History';
        $this->view->histories = $histories;
        echo $this->view->render('history');
    }
}
