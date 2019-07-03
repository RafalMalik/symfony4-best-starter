<?php
namespace App\Tests\Form;

use App\Form\ChangePasswordType;
use App\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class ChangePasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'test' => 'test',
            'test2' => 'test2',
        ];

        $objectToCompare = array();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ChangePasswordType::class, $objectToCompare);

        $object = array();
        // ...populate $object properties with the data stored in $formData

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);
    }
}