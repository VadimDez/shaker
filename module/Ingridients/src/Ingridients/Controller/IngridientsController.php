<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:37 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Ingridients\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Ingridients\Model\Ingridients;          // <-- Add this import
use Ingridients\Form\IngridientsForm;       // <-- Add this import

use Zend\Validator\File\Size;

class IngridientsController extends AbstractActionController
{
    protected $ingridientsTable;

    public function getIngridientsTable()
    {
        if (!$this->ingridientsTable) {
            $sm = $this->getServiceLocator();
            $this->ingridientsTable = $sm->get('Ingridients\Model\IngridientsTable');
        }
        return $this->ingridientsTable;
    }

    public function indexAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $ingridients = $this->getIngridientsTable()->fetchAll();

        $model = new Ingridients();

        return new ViewModel(array(
            'ingridients'    => $model->orderIngridientsByCategory($ingridients,$model->getCategoriesName($adapter))
        ));
    }

    // Add content to this method:
    public function addAction()
    {
        //@todo refactoring!!! change algoritm and solve some problem
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new IngridientsForm($adapter);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ingridients = new Ingridients();
            $form->setInputFilter($ingridients->getInputFilter());


            $nonFile = $request->getPost()->toArray();
            $File    = $this->params()->fromFiles('ingridientImageAdress');
            $minImg    = $this->params()->fromFiles('ingridientMinImageAdress');
            $data    = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            //set data post and file ...
            $form->setData($data);

            if ($form->isValid()) {
                $size = new Size(array('min'=>20)); //minimum bytes filesize

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapterMin = new \Zend\File\Transfer\Adapter\Http();
                //validator can be more than one...
                $adapter->setValidators(array($size), $File['name']);
                $adapterMin->setValidators(array($size), $minImg['name']);
                if (!$adapter->isValid() || !$adapterMin->isValid()){

                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach($dataError as $key=>$row)
                    {
                        $error[] = $row;
                    } //set formElementErrors
                    $form->setMessages(array('ingridientImageAdress'=>$error ));
                    $form->setMessages(array('ingridientMinImageAdress'=>$error ));
                } else {
                    $adapter->setDestination('./data/ingridientImages');
                    $adapterMin->setDestination('./data/ingridientMinImages');
                    $count = 0;
                    foreach ($adapter->getFileInfo() as $info)
                    {
                        if($count == 0)
                        {
                            $validator = new \Zend\Validator\File\Exists('./data/ingridientImages');

                            $ckeckIfExist = false;
                            $rand = rand(1,1000);

                            while($ckeckIfExist == false)
                            {

                                // image adress
                                $imgAdress = './data/ingridientImages/' . $rand . '.png';

                                // Perform validation
                                if (!$validator->isValid($imgAdress)) {
                                    //
                                    $ckeckIfExist = true;

                                    // file is valid
                                    $adapter->addFilter('File\Rename',
                                        array('target' => $imgAdress,
                                            'overwrite' => true));
                                    if ($adapter->receive($info['name'])) {
                                        $ingridients->exchangeArray($form->getData());

                                        // image adress
                                        //@todo solve this problem
                                        //$ingridients->ingridientImageAdress = $rand . '.png';
                                        $adressForLargeImage = $rand . '.png';
                                        $count++;
                                    }
                                }
                                else
                                {
                                    // if exist random ID
                                    // regenerate it
                                    $rand = rand(1,1000);
                                }
                            }
                        }
                        elseif($count == 1)
                        {
                            $validator = new \Zend\Validator\File\Exists('./data/ingridientMinImages');

                            $ckeckIfExist = false;
                            $rand = rand(1,1000);

                            while($ckeckIfExist == false)
                            {

                                // image adress
                                $imgAdress = './data/ingridientMinImages/' . $rand . '.png';

                                // Perform validation
                                if (!$validator->isValid($imgAdress)) {
                                    //
                                    $ckeckIfExist = true;

                                    // file is valid
                                    $adapterMin->addFilter('File\Rename',
                                        array('target' => $imgAdress,
                                            'overwrite' => true));
                                    if ($adapterMin->receive($info['name'])) {
                                        $ingridients->exchangeArray($form->getData());

                                        // image adress
                                        $ingridients->ingridientMinImageAdress = $rand . '.png';
                                        $count++;
                                    }
                                }
                                else
                                {
                                    // if exist random ID
                                    // regenerate it
                                    $rand = rand(1,1000);
                                }
                            }
                        }

                    }
                    $ingridients->ingridientImageAdress = $adressForLargeImage;
                    $this->getIngridientsTable()->saveIngridients($ingridients);
                }


                // Redirect to list of ingridientss
                return $this->redirect()->toRoute('ingridients');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('ingridients', array(
                'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $ingridients = $this->getIngridientsTable()->getIngridients($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('ingridients', array(
                'action' => 'index'
            ));
        }
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form  = new IngridientsForm($adapter);
        $form->bind($ingridients);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($ingridients->getInputFilter());

            // img

            $nonFile = $request->getPost()->toArray();
            $File    = $this->params()->fromFiles('ingridientImage');
            $data    = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            /** if you're using ZF >= 2.1.1
             *  you should update to the latest ZF2 version
             *  and assign $data like the following
            $data    = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
            );
             */

            //set data post and file ...
            $form->setData($data);

            // img

            $form->setData($request->getPost());

            if ($form->isValid()) {

                // img

                $size = new Size(array('min'=>20)); //minimum bytes filesize

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                //validator can be more than one...
                $adapter->setValidators(array($size), $File['name']);

                if (!$adapter->isValid()){

                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach($dataError as $key=>$row)
                    {
                        $error[] = $row;
                    } //set formElementErrors
                    $form->setMessages(array('ingridientImage'=>$error ));
                } else {

                    $adapter->setDestination('./data/ingridientImages');

                    foreach ($adapter->getFileInfo() as $info)
                    {
                        $validator = new \Zend\Validator\File\Exists('./data/ingridientImages');

                        $ckeckIfExist = false;
                        $rand = rand(1,1000);

                        while($ckeckIfExist == false)
                        {

                            // image adress
                            $imgAdress = './data/ingridientImages/' . $rand . '.png';

                            // Perform validation
                            if (!$validator->isValid($imgAdress)) {
                                //
                                $ckeckIfExist = true;

                                // file is valid
                                $adapter->addFilter('File\Rename',
                                    array('target' => $imgAdress,
                                        'overwrite' => true));
                                if ($adapter->receive($info['name'])) {
                                    $ingridients->exchangeArray($form->getData());

                                    // image address
                                    $ingridients->ingridientImageAdress = $rand . '.png';
                                    $ingridients->idIngridient = $id;
                                    $this->getIngridientsTable()->saveIngridients($ingridients);
                                }
                            }
                            else
                            {
                                // if exist random ID
                                // regenerate it
                                $rand = rand(1,1000);
                            }
                        }


                    }
                }

                // img


                //$this->getIngridientsTable()->saveIngridients($ingridients);

                // Redirect to list of albums
                return $this->redirect()->toRoute('ingridients');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('ingridients');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getIngridientsTable()->deleteIngridients($id);
            }

            // Redirect to list of ingridientss
            return $this->redirect()->toRoute('ingridients');
        }

        return array(
            'id'    => $id,
            'ingridients' => $this->getIngridientsTable()->getIngridients($id)
        );
    }


    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {

            return $this->redirect()->toRoute('ingridients', array(
                'action' => 'index'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $ingridient = $this->getIngridientsTable()->getIngridients($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('cocktail', array(
                'action' => 'index'
            ));
        }

        return new ViewModel(array(
            'ingridient' => $ingridient,
        ));

    }

}