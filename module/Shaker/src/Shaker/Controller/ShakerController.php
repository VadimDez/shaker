<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/21/13
 * Time: 10:23 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Shaker\Controller;

use Shaker\Model\Pull;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShakerController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function shakeAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        if ($request->isPost())
        {
            $string = mysql_real_escape_string($this->params()->fromPost('param1'));

            $pull = new Pull();

            $results = $pull->getCocktailsByIngridient($string,$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

            $response->setContent(json_encode($results));

            return $response;
        }
        else
        {
            return new ViewModel();
        }
    }
}