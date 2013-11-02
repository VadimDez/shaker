<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 12:02 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Controller;

use Admin\Form\AdminFormIngridients;
use Admin\Form\AdminFormStuff;
use Admin\Model\Category;
use Admin\Model\DataLoader;
use Admin\Model\FolderActions;
use Admin\Model\Usage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Admin;
use Admin\Form\AdminForm;
use Zend\Validator\File\Size;
use Zend\Db\Adapter\Driver;
use Admin\Form\AdminFormCategory;
use Admin\Model\Inv;

class AdminController extends AbstractActionController
{
    protected $adminTable;
    protected $ingridientsTable;
    protected $usageTable;
    protected $categoryTable;
    protected $stuffTable;
    protected $invTable;

    public function getTable($table,$path)
    {
        if (!$table) {
            $sm = $this->getServiceLocator();
            $table = $sm->get($path);
        }
        return $table;
    }

    /*
     *  Cocktail part
     *  index, add, edit, delete
     */
    public function indexAction()
    {
        // show all the stuff and possible operations
        return new ViewModel(array(
            'admins'        => $this->getTable($this->adminTable,'Admin\Model\AdminTable')->fetchAll(),
            'ingridients'   => $this->getTable($this->ingridientsTable,'Ingridients\Model\IngridientsTable')->fetchAll(),
            'categories'    => $this->getTable($this->categoryTable,'Admin\Model\CategoryTable')->fetchAll(),
            'stuffs'        => $this->getTable($this->stuffTable,'Stuff\Model\StuffTable')->fetchAll()
        ));
    }

    public function addAction()
    {
        // save cocktail's data into db,
        // and image into folder with path like /images/cocktailName/Big.png
        $form = new AdminForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $admin = new Admin();
            $folder= new FolderActions();
            $form->setInputFilter($admin->getInputFilter());

            $nonFile = $request->getPost()->toArray();
            $File    = $this->params()->fromFiles('cocktailImage');
            $data    = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            //set data post and file ...
            $form->setData($data);

            if ($form->isValid()) {
                $size = new Size(array('min'=>10000)); //set minimum bytes filesize

                $adapter = new \Zend\File\Transfer\Adapter\Http();

                $adapter->setValidators(array($size), $File['name']);

                if (!$adapter->isValid())
                {
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach($dataError as $key=>$row)
                    {
                        $error[] = $row;
                    }
                    //set formElementErrors
                    $form->setMessages(array('cocktailImage'=>$error ));
                } else {
                    $path = './data/cocktailImages';
                    $adapter->setDestination($path);

                    foreach ($adapter->getFileInfo() as $info)
                    {
                        $name = $this->params()->fromPost('cocktailName');
                        $myFolder = $folder->createFolderAndReturnFolderName($name,$path);
                        // image address
                        $imgAddress = $myFolder . '/Big.png';

                        // Perform validation
                        $validator = new \Zend\Validator\File\Exists($path);
                        if (!$validator->isValid($imgAddress))
                        {   // file is valid
                            $adapter->addFilter('File\Rename',
                                array('target' => $imgAddress,
                                    'overwrite' => true));

                            if ($adapter->receive($info['name']))
                            {
                                $admin->exchangeArray($form->getData());
                                // image address
                                $admin->cocktailImageAdress = $name . '/Big.png';
                                $this->getTable($this->adminTable,'Admin\Model\AdminTable')->saveCocktail($admin);
                            }
                        }
                    }
                }
                // Redirect to list of admins
                return $this->redirect()->toRoute('admin');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'add'
            ));
        }

        try {
            $admin = $this->getTable($this->adminTable,'Admin\Model\AdminTable')->getCocktail($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'index'
            ));
        }

        $form  = new AdminForm();
        $form->bind($admin);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($admin->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getTable($this->adminTable,'Admin\Model\AdminTable')->saveCocktail($admin);

                // Redirect to list of cocktails
                return $this->redirect()->toRoute('admin');
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
            'cocktailinfo' => $admin
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin');
        }
        $cocktail = $this->getTable($this->adminTable,'Admin\Model\AdminTable')->getCocktail($id);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                // delete folder and files in it
                $folder = new FolderActions();
                $folder->deleteFolder($cocktail->cocktailName, './data/cocktailImages');
                // delete stuff from DB
                $id = (int) $request->getPost('id');
                $this->getTable($this->adminTable,'Admin\Model\AdminTable')->deleteCocktail($id);
            }

            // Redirect to list of admins
            return $this->redirect()->toRoute('admin');
        }

        return array(
            'id'    => $id,
            'admin' => $cocktail
        );
    }

    /*
     *  Ingridients part
     */
    public function ingridientsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin');
        }
        $load    = new DataLoader();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form    = new AdminFormIngridients($adapter);
        // sql
        $select  = "id, u.idCocktail, u.idIngridient, quantity, ingridientName";
        $from    = "used as u , cocktails as c , ingridients as i";
        $where   = "u.idCocktail = c.idCocktail AND u.idIngridient = i.idIngridient AND u.idCocktail=?";
        $mySql   = "SELECT $select FROM $from WHERE $where";
        $records = $load->returnRecords($adapter,$id,$mySql);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $usage = new Usage();
            $form->setInputFilter($usage->getInputFilter());
            $form->setData($request->getPost());


            if ($form->isValid()) {
                $usage->exchangeArray($form->getData());
                $usage->idCocktail = $id;
                $this->getTable($this->usageTable,'Admin\Model\UsageTable')->saveUsage($usage);

                // Redirect to list of albums
                return $this->redirect()->toRoute('admin');
            }
        }
        return array(
            'id'   => $id,
            'form' => $form,
            'used' => $records
        );
    }

    public function deleteingridientAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin');
        }
        $table = $this->getTable($this->usageTable,'Admin\Model\UsageTable');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $table->deleteUsage($id);
            }

            // Redirect to list of admins
            return $this->redirect()->toRoute('admin');
        }
        return array(
            'id'    => $id,
            'admin' => $table->getUsage($id)
        );
    }

    /*
     *  Functions that are used by ajax to load more data dynamically
     *  return an json that contains limited number of elements
     *
     *  if you try to access to path this shows empty view.
     */
    public function  morecocktailsAction()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost())
        {
            $adapter  = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $moreData = new DataLoader();
            return $response->setContent(json_encode($moreData->loadDataDynamically($adapter,$this->params()->fromPost('limit'),$this->params()->fromPost('offset'),'cocktails')));
        }
        else
        {
            return new ViewModel();
        }
    }

    public function  morecategoriesAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost())
        {
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $moreData = new DataLoader();
            return $response->setContent(json_encode($moreData->loadDataDynamically($adapter,$this->params()->fromPost('limit'),$this->params()->fromPost('offset'),'categories')));
        }
        else
        {
            return new ViewModel();
        }
    }

    public function  moreingridientsAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost())
        {
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $moreData = new DataLoader();
            return $response->setContent(json_encode($moreData->loadDataDynamically($adapter,$this->params()->fromPost('limit'),$this->params()->fromPost('offset'),'ingridients')));
        }
        else
        {
            return new ViewModel();
        }
    }

    /*
     * Categories part
     * Add, edit, delete
     */
    public function addcategoryAction()
    {
        $form = new AdminFormCategory();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $category = new Category();
            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $category->exchangeArray($form->getData());
                $this->getTable($this->categoryTable,'Admin\Model\CategoryTable')->saveCategory($category);

                // Redirect to list of albums
                return $this->redirect()->toRoute('admin');
            }
        }
        return array('form' => $form);
    }

    public function editcategoryAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'addcategory'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $category = $this->getTable($this->categoryTable,'Admin\Model\CategoryTable')->getCategory($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'index'
            ));
        }

        $form  = new AdminFormCategory();
        $form->bind($category);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getTable($this->categoryTable,'Admin\Model\CategoryTable')->saveCategory($category);

                return $this->redirect()->toRoute('admin');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'category' => $category
        );
    }

    public function deletecategoryAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getTable($this->categoryTable,'Admin\Model\CategoryTable')->deleteCategory($id);
            }
            return $this->redirect()->toRoute('admin');
        }

        return array(
            'id'    => $id,
            'category' => $this->getTable($this->categoryTable,'Admin\Model\CategoryTable')->getCategory($id)
        );
    }

    /*
     *  Stuff part
     */
    public function stuffAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin');
        }
        $load = new DataLoader();

        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form      = new AdminFormStuff($adapter);
        $form->get('submit')->setValue('Add');

        //$stmt = $sql->prepareStatementForSqlObject($select);
        $select = "idInvUsage, u.idCocktail, u.idStuff, stuffName";
        $from   = "inventaryUsage as u , cocktails as c , stuff as s";
        $where  = "u.idCocktail = c.idCocktail AND u.idStuff = s.idStuff AND u.idCocktail=?";
//        $resultQuery = new ResultSet();
//        $resultQuery = $resultQuery->initialize($stmt->execute());
        $records = $load->returnRecords($adapter,$id,"SELECT $select FROM $from WHERE $where");
        $request = $this->getRequest();

        if ($request->isPost())
        {
            $inv = new Inv();
            $form->setInputFilter($inv->getInputFilter());
            $form->setData($request->getPost());
//            var_dump($request->getPost());
//            die();
            if ($form->isValid()) {

                $inv->exchangeArray($form->getData());
                $inv->idCocktail = $id;
                $this->getTable($this->invTable,'Admin\Model\InvTable')->saveInv($inv);
                // Redirect
                return $this->redirect()->toRoute('admin');
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
            'used' => $records
        );

    }
}