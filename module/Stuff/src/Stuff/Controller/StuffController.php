<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:37 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Stuff\Controller;

use Admin\Model\FolderActions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Stuff\Model\Stuff;          // <-- Add this import
use Stuff\Form\StuffForm;       // <-- Add this import

use Zend\Validator\File\Size;

class StuffController extends AbstractActionController
{
    protected $stuffTable;

    public function getStuffTable()
    {
        if (!$this->stuffTable) {
            $sm = $this->getServiceLocator();
            $this->stuffTable = $sm->get('Stuff\Model\StuffTable');
        }
        return $this->stuffTable;
    }

    public function indexAction()
    {
        $stuff = $this->getStuffTable()->fetchAll();

        return new ViewModel(array(
            'stuff'    => $stuff
        ));
    }

    /*
     * Stuff part:
     * add, edit, delete
     */
    public function addAction()
    {
        //@todo refactoring!!!
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new StuffForm($adapter);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $stuff = new Stuff();
            $form->setInputFilter($stuff->getInputFilter());


            $nonFile = $request->getPost()->toArray();
            $File    = $this->params()->fromFiles('stuffImageAdress');
            $minImg    = $this->params()->fromFiles('stuffMinImageAdress');
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
                    $form->setMessages(array('stufftImageAdress'=>$error ));
                    $form->setMessages(array('stuffMinImageAdress'=>$error ));
                } else {
                    $path = './data/stuffImages';
                    $adapter->setDestination($path);
                    $folder = new FolderActions();

                    $count = 0;
                    foreach ($adapter->getFileInfo() as $info)
                    {
                        // save two images of different sizes
                        // name
                        $name = $this->params()->fromPost('stuffName');
                        $myFolder = $folder->createFolderAndReturnFolderName($name,$path);
                        if($count == 0)
                        {
                            $size = '/Big.png';
                        }
                        else
                        {
                            $size = '/min.png';
                        }

                        $validator = new \Zend\Validator\File\Exists($path);
                        // image adress
                        $imgAddress = $myFolder . $size;

                        // Perform validation
                        if (!$validator->isValid($imgAddress))
                        {
                            // file is valid
                            $adapter->addFilter('File\Rename',
                                array('target' => $imgAddress,
                                    'overwrite' => true));
                            if ($adapter->receive($info['name'])) {
                                $stuff->exchangeArray($form->getData());

                                // image adress
                                if($count == 0)
                                {
                                    $stuff->stuffImageAdress = $name . '/Big.png';
                                }
                                else
                                {
                                    $stuff->stuffMinImageAdress = $name . '/min.png';
                                }
                                $count++;
                            }
                        }
                    }


                    $this->getStuffTable()->saveStuff($stuff);
                }

                // Redirect to list of stuffs
                return $this->redirect()->toRoute('stuff');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('stuff', array(
                'action' => 'add'
            ));
        }

        try {
            $stuff = $this->getStuffTable()->getStuff($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('stuff', array(
                'action' => 'index'
            ));
        }
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form  = new StuffForm($adapter);
        $form->bind($stuff);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($stuff->getInputFilter());

            // img

            $nonFile = $request->getPost()->toArray();
            $File    = $this->params()->fromFiles('ingridientImage');
            $data    = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );


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
                    $path = './data/ingridientImages';
                    $adapter->setDestination($path);

                    foreach ($adapter->getFileInfo() as $info)
                    {
                        $name = $this->params()->fromPost('stuffName');
                        $folder = new FolderActions();
                        $myFolder = $folder->createFolderAndReturnFolderName($name,$path);
                        // image address
                        $imgAddress = $myFolder . '/Big.png';

                        // Perform validation
                        $validator = new \Zend\Validator\File\Exists($path);
                        if (!$validator->isValid($imgAddress))
                        {

                            // file is valid
                            $adapter->addFilter('File\Rename',
                                array('target' => $imgAddress,
                                    'overwrite' => true));
                            if ($adapter->receive($info['name'])) {
                                $stuff->exchangeArray($form->getData());

                                // image adress
                                $stuff->ingridientImageAdress = $imgAddress;
                                $stuff->idIngridient = $id;
                                $this->getStuffTable()->saveStuff($stuff);
                            }
                        }
                    }
                }
                return $this->redirect()->toRoute('stuff');
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
            return $this->redirect()->toRoute('stuff');
        }

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes')
            {
                // delete folder and files in it
                $stuff = $this->getStuffTable()->getStuff($id);
                $folder = new FolderActions();
                $folder->deleteFolder($stuff->stuffName,'./data/stuffImages/');

                // delete stuff from DB
                $id = (int) $request->getPost('id');
                $this->getStuffTable()->deleteStuff($id);
            }
            // Redirect to list of stuffs
            return $this->redirect()->toRoute('admin');
        }

        return array(
            'id'    => $id,
            'stuff' => $this->getStuffTable()->getStuff($id)
        );
    }


    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {

            return $this->redirect()->toRoute('stuff', array(
                'action' => 'index'
            ));
        }

        try {
            $stuff = $this->getStuffTable()->getStuff($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('cocktail', array(
                'action' => 'index'
            ));
        }

        return new ViewModel(array(
            'stuff' => $stuff,
        ));

    }

}