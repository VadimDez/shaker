<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/21/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Shaker\Model;
// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;



class Shaker implements InputFilterAwareInterface
{
    public $id;
    public $artist;
    public $title;
    protected $inputFilter;


    public function getIngridients() // <-- Add variable to this
    {

    }

    public function getCocktailsByIngridient($ingridient, $adapter)
    {
        $ingridient = str_replace(' ',"",$ingridient);
        //$temp = str_replace(',',"' AND i.ingridientName = '",$ingridient);
        $temp = str_replace(',',"','",$ingridient);
        $mySql =   "SELECT *
                    FROM used as u, cocktails as c, ingridients as i
                    WHERE i.idIngridient = u.idIngridient AND u.idCocktail = c.idCocktail AND ingridientName IN ('$temp')
                    GROUP BY c.idCocktail";

        $resultQuery = $adapter->query($mySql)->execute();
        $records = array();
        foreach($resultQuery as $res)
        {
            $records[] = $res;
        }
        return $records;
    }

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->artist = (!empty($data['artist'])) ? $data['artist'] : null;
        $this->title  = (!empty($data['title'])) ? $data['title'] : null;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'artist',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}