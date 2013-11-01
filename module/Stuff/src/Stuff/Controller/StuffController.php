<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:37 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Stuff\Controller;

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

    // Add content to this method:
    public function addAction()
    {
        //@todo refactoring!!! change algoritm and solve some problem
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

            /** if you're using ZF >= 2.1.1
             *  you should update to the latest ZF2 version
             *  and assign $data like the following
            $data    = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
            );
             */
$adressForLargeImage="";
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
                    $adapter->setDestination('./data/ingridientImages');
                    $adapterMin->setDestination('./data/ingridientMinImages');
                    $count = 0;
                    foreach ($adapter->getFileInfo() as $info)
                    {
                        // name
                        $name = $this->params()->fromPost('stuffName');
                        $name = str_replace(' ','_',$name);
                        // check if folder exist, and if dont - create folder
                        $myFolder = './data/stuffImages/' . $name;
                        If(!file_exists($myFolder))
                        {
                            mkdir($myFolder);
                        }

                        if($count == 0)
                        {
                            $validator = new \Zend\Validator\File\Exists($myFolder);


                                // image adress
                                $imgAdress = $myFolder . '/Big.png';

                                // Perform validation
                                if (!$validator->isValid($imgAdress)) {
                                    //

                                    // file is valid
                                    $adapter->addFilter('File\Rename',
                                        array('target' => $imgAdress,
                                            'overwrite' => true));
                                    if ($adapter->receive($info['name'])) {
                                        $stuff->exchangeArray($form->getData());

                                        // image adress
                                        //@todo solve this problem
                                        //$stuff->ingridientImageAdress = $rand . '.png';
                                        $adressForLargeImage = $name . '/Big.png';
                                        $count++;
                                    }
                                }
                        }
                        elseif($count == 1)
                        {
                            $validator = new \Zend\Validator\File\Exists($myFolder);

                                // image adress
                                $imgAdress = $myFolder . '/min.png';

                                // Perform validation
                                if (!$validator->isValid($imgAdress)) {
                                    //
                                    // file is valid
                                    $adapterMin->addFilter('File\Rename',
                                        array('target' => $imgAdress,
                                            'overwrite' => true));
                                    if ($adapterMin->receive($info['name'])) {
                                        $stuff->exchangeArray($form->getData());

                                        // image adress
                                        $stuff->stuffMinImageAdress = $name . '/min.png';
                                        $count++;
                                    }
                                }

                        }

                    }

                    $stuff->stuffImageAdress = $adressForLargeImage;
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

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
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
                                    $stuff->exchangeArray($form->getData());

                                    // image adress
                                    $stuff->ingridientImageAdress = $rand . '.png';
                                    $stuff->idIngridient = $id;
                                    $this->getStuffTable()->saveStuff($stuff);
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


                //$this->getStuffTable()->saveStuff($stuff);

                // Redirect to list of albums
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

            if ($del == 'Yes') {

                // delete folder and files in it
                $stuff = $this->getStuffTable()->getStuff($id);
                $dirPath = $stuff->stuffName;
                $dirPath = './data/stuffImages/' . str_replace(' ','_',$dirPath);

                if (! is_dir($dirPath)) {
                    throw new InvalidArgumentException("$dirPath must be a directory");
                }

                if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                    $dirPath .= '/';
                }
                $files = glob($dirPath . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        self::deleteDir($file);
                    } else {
                        unlink($file);
                    }
                }
                rmdir($dirPath);

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

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
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