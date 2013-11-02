<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 11:53 AM
 * To change this template use File | Settings | File Templates.
 */


namespace Cocktail\Controller;

use Cocktail\Model\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CocktailController extends AbstractActionController
{
    protected $cocktailTable;

    public function getCocktailTable()
    {
        if (!$this->cocktailTable) {
            $sm = $this->getServiceLocator();
            $this->cocktailTable = $sm->get('Cocktail\Model\CocktailTable');
        }
        return $this->cocktailTable;
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'cocktails' => $this->getCocktailTable()->fetchAll(),
        ));
    }

    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'index'
            ));
        }

        try {
            $cocktails = $this->getCocktailTable()->getCocktail($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'index'
            ));
        }
        return new ViewModel(array(
            'cocktails' => $cocktails
        ));
    }

    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id)
        {
            return $this->redirect()->toRoute('cocktail', array(
                'action' => 'index'
            ));
        }

        try {
            $cocktails = $this->getCocktailTable()->getCocktail($id);
        }
        catch (\Exception $ex)
        {
            return $this->redirect()->toRoute('cocktail', array(
                'action' => 'index'
            ));
        }

        // pull all ingridients
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $mySql = "SELECT i.idIngridient, ingridientName, quantity FROM used as u, ingridients as i WHERE u.idCocktail=? AND u.idIngridient=i.idIngridient";
        $resultQuery = $adapter->query($mySql)->execute(array($id));
        $records = array();
        foreach($resultQuery as $res)
        {
            $records[] = $res;
        }
        return new ViewModel(array(
            'cocktail'      => $cocktails,
            'ingridients'   => $records
        ));
    }

    public function loadAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost())
        {
            $results = new Result();
            $results = $results->getQueryResult($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),$this->params()->fromPost('param1'),$this->params()->fromPost('param2'),$this->params()->fromPost('param3'));
            $records = array();
            foreach($results as $res)
            {
                $records[] = $res;
            }

            $results = array(
                'return1' => $records
            );
            $response->setContent(json_encode($results));
            return $response;
        }
        else
        {
            return new ViewModel();
        }
    }

}