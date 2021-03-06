<?php
/**
 * @author Jonathan Moss <xirisr@gmail.com>
 * @copyright 2010 Jonathan Moss
 * @package Morph
 */

require_once dirname(__FILE__).'/../Morph.phar';
require_once dirname(__FILE__).'/mongoUnit/TestCase.php';
require_once dirname(__FILE__).'/test-objects/ComposeOneParent.php';
require_once dirname(__FILE__).'/test-objects/Child.php';

/**
 * @package Morph
 */
class TestComposeOne extends \mongoUnit\TestCase
{

    public function setup()
    {
        parent::setup();
        \morph\Storage::init($this->getDatabase());
    }

    public function tearDown()
    {
        parent::tearDown();
        \morph\Storage::deInit();
    }

    public function testStoresParentAndChild()
    {
        $parent = new ComposeOneParent();
        $parent->Name = 'Compose One Parent';

        $child = new Child();
        $child->Name = 'Child';

        $parent->Child = $child;

        $parent->save();
        $this->assertCollectionExists('ComposeOneParent');
        $this->assertCollectionDoesNotExist('Child');

        $this->assertDocumentExists('ComposeOneParent', $parent->id());

        $this->assertDocumentPropertyEquals(array('_ns'=>'Child', 'Name'=>'Child', 'Age' => null), 'ComposeOneParent', 'Child', $parent->id());
    }

}