<?php
/**
 * Created by PhpStorm.
 * User: CHI-DT
 * Date: 18/07/2018
 * Time: 16:01
 */

namespace Application\Form;


use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

class CommentForm extends Form
{


    /**
     * CommentForm constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('comment-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    // This method adds elements to form (input fields and submit button)
    protected function addElements()
    {
//        // Add "author" field
//        $this->add([
//            'type' => Text::class,
//            'name' => 'author',
//            'attributes' => [
//                'id' => 'author'
//            ],
//            'options' => [
//                'label' => 'Author'
//            ]
//        ]);

        // Add "comment" field
        $this->add([
            'type' => Textarea::class,
            'name' => 'comment',
            'attributes' => [
                'id' => 'comment'
            ],
            'options' => [
                'label' => 'Comment'
            ]
        ]);

        // Add the submit button
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id' => 'submitbutton'
            ]
        ]);
    }

    // This method creates input filter (used for form filtering/validation).
    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

//        $inputFilter->add([
//            'name' => 'author',
//            'required' => true,
//            'filters' => [
//                ['name' => StringTrim::class]
//            ],
//            'validators' => [
//                [
//                    'name' => StringLength::class,
//                    'options' => [
//                        'min' => 1,
//                        'max' => 128
//                    ]
//                ]
//            ]
//        ]);

        $inputFilter->add([
            'name' => 'comment',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class]
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 4096
                    ]
                ]
            ]
        ]);
    }
}