<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:37 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Ingridients\Controller;

use Admin\Model\FolderActions;
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

                $size = new Size(array('min'=>200)); //minimum bytes filesize

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
                    $form->setMessages(array('stufftImageAdress'=>$error ));
                    $form->setMessages(array('stuffMinImageAdress'=>$error ));
                } else {
                    $path = './data/ingridientImages';
                    $adapter->setDestination($path);
                    $folder = new FolderActions();

                    $count = 0;
                    foreach ($adapter->getFileInfo() as $info)
                    {
                        // save two images of different sizes
                        // name
                        $name = $this->params()->fromPost('ingridientName');
                        $myFolder = $folder->createFolderAndReturnFolderName($name,$path);
                        if($count == 0)
                        {
                            $isize = '/Big.png';
                        }
                        else
                        {
                            $isize = '/min.png';
                        }

                        $validator = new \Zend\Validator\File\Exists($path);
                        // image adress
                        $imgAddress = $myFolder . $isize;
                        // Perform validation
                        if (!$validator->isValid($imgAddress))
                        {
                            // file is valid
                            $adapter->addFilter('File\Rename',
                                array('target' => $imgAddress,
                                    'overwrite' => true));

                            if ($adapter->receive($info['name'])) {
                                $name = str_replace(' ','_',$name);
                                // image adress
                                if($count == 0)
                                {
                                    $ingridients->exchangeArray($form->getData()); // todo solve this!
                                    $ingridients->ingridientImageAdress = $name . $isize;
                                }
                                else
                                {
                                    $ingridients->ingridientMinImageAdress = $name . $isize;
                                }
                                $count++;
                            }
                        }

                    }
                    try {
                        $this->getIngridientsTable()->saveIngridients($ingridients);
                    }
                    catch (\Exception $ex) {
                        return $this->redirect()->toRoute('ingridients', array(
                            'action' => 'index'
                        ));
                    }

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
                // delete folder and files in it
                $id = (int) $request->getPost('id');

                $ingredient = $this->getIngridientsTable()->getIngridients($id);
                $folder = new FolderActions();
                $folder->deleteFolder($ingredient->ingridientName,'./data/ingridientImages/');

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

        if (!$id)
        {
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