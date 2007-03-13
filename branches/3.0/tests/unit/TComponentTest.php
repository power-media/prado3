<?php
require_once dirname(__FILE__).'/phpunit2.php';

class NewComponent extends TComponent {
  private $_object = null;
  private $_text = 'default';
  private $_eventHandled = false;

  public function getText() {
    return $this->_text;
  }
  
  public function setText($value) {
    $this->_text=$value;
  }
  
  public function getObject() {
    if(!$this->_object) {
      $this->_object=new NewComponent;
      $this->_object->_text='object text';
    }
    return $this->_object;
  }
  
  public function onMyEvent($param) {
    $this->raiseEvent('OnMyEvent',$this,$param);
  }
  
  public function myEventHandler($sender,$param) {
    $this->_eventHandled=true;
  }
  
  public function isEventHandled() {
    return $this->_eventHandled;
  }
}

/**
 * @package System
 */
class TComponentTest extends PHPUnit2_Framework_TestCase {
  
  protected $component;

  public function setUp() {
    $this->component = new NewComponent();
  }

  public function tearDown() {
    $this->component = null;
  }

  public function testHasProperty() {
    $this->assertTrue($this->component->hasProperty('Text'), "Component hasn't property Text");
    $this->assertTrue($this->component->hasProperty('text'), "Component hasn't property text");
    $this->assertFalse($this->component->hasProperty('Caption'), "Component as property Caption");
  }

  public function testCanGetProperty() {
    $this->assertTrue($this->component->canGetProperty('Text'));
    $this->assertTrue($this->component->canGetProperty('text'));
    $this->assertFalse($this->component->canGetProperty('Caption'));
  }

  public function testCanSetProperty() {
    $this->assertTrue($this->component->canSetProperty('Text'));
    $this->assertTrue($this->component->canSetProperty('text'));
    $this->assertFalse($this->component->canSetProperty('Caption'));
  }

  public function testGetProperty() {
    $this->assertTrue('default'===$this->component->Text);
    try {
      $value2=$this->component->Caption;
      $this->fail('exception not raised when getting undefined property');
    } catch(TInvalidOperationException $e) {
    }
  }
  
  public function testSetProperty() {
    $value='new value';
    $this->component->Text=$value;
    $text=$this->component->Text;
    $this->assertTrue($value===$this->component->Text);
    try {
      $this->component->NewMember=$value;
      $this->fail('exception not raised when setting undefined property');
    } catch(TInvalidOperationException $e) {
    }
  }

  public function testGetSubProperty() {
    $this->assertTrue('object text'===$this->component->getSubProperty('Object.Text'));
  }
  
  public function testSetSubProperty() {
    $this->component->setSubProperty('Object.Text','new object text');
    $this->assertEquals('new object text',$this->component->getSubProperty('Object.Text'));
  }
  
  public function testHasEvent() {
    $this->assertTrue($this->component->hasEvent('OnMyEvent'));
    $this->assertTrue($this->component->hasEvent('onmyevent'));
    $this->assertFalse($this->component->hasEvent('onYourEvent'));
  }

  public function testHasEventHandler() {
    $this->assertFalse($this->component->hasEventHandler('OnMyEvent'));
    $this->component->attachEventHandler('OnMyEvent','foo');
    $this->assertTrue($this->component->hasEventHandler('OnMyEvent'));
  }

  public function testGetEventHandlers() {
    $list=$this->component->getEventHandlers('OnMyEvent');
    $this->assertTrue(($list instanceof TList) && ($list->getCount()===0));
    $this->component->attachEventHandler('OnMyEvent','foo');
    $this->assertTrue(($list instanceof TList) && ($list->getCount()===1));
    try {
      $list=$this->component->getEventHandlers('YourEvent');
      $this->fail('exception not raised when getting event handlers for undefined event');
    } catch(TInvalidOperationException $e) {
    }
  }

  public function testAttachEventHandler() {
    $this->component->attachEventHandler('OnMyEvent','foo');
    $this->assertTrue($this->component->getEventHandlers('OnMyEvent')->getCount()===1);
    try {
      $this->component->attachEventHandler('YourEvent','foo');
      $this->fail('exception not raised when attaching event handlers for undefined event');
    } catch(TInvalidOperationException $e) {
    }
              /*$this->component->MyEvent[]='foo2';
		$this->assertTrue($this->component->getEventHandlers('MyEvent')->getCount()===2);
		$this->component->getEventHandlers('MyEvent')->add('foo3');
		$this->assertTrue($this->component->getEventHandlers('MyEvent')->getCount()===3);
		$this->component->MyEvent[0]='foo4';
		$this->assertTrue($this->component->getEventHandlers('MyEvent')->getCount()===3);
		$this->component->getEventHandlers('MyEvent')->insert(0,'foo5');
		$this->assertTrue($this->component->MyEvent->Count===4 && $this->component->MyEvent[0]==='foo5');
		$this->component->MyEvent='foo6';
		$this->assertTrue($this->component->MyEvent->Count===5 && $this->component->MyEvent[4]==='foo6');*/
  }

  public function testRaiseEvent() {
    $this->component->attachEventHandler('OnMyEvent',array($this->component,'myEventHandler'));
    $this->assertFalse($this->component->isEventHandled());
    $this->component->raiseEvent('OnMyEvent',$this,null);
    $this->assertTrue($this->component->isEventHandled());
    $this->component->attachEventHandler('OnMyEvent',array($this->component,'Object.myEventHandler'));
    $this->assertFalse($this->component->Object->isEventHandled());
    $this->component->raiseEvent('OnMyEvent',$this,null);
    $this->assertTrue($this->component->Object->isEventHandled());
  }

  public function testEvaluateExpression() {
    $expression="1+2";
    $this->assertTrue(3===$this->component->evaluateExpression($expression));
    try {
      $button=$this->component->evaluateExpression('$this->button');
      $this->fail('exception not raised when evaluating an invalid exception');
    } catch(Exception $e) {
    }
  }
  
  public function testEvaluateStatements() {
    $statements='$a="test string"; echo $a;';
    $this->assertEquals('test string',$this->component->evaluateStatements($statements));
    try {
      $statements='$a=new NewComponent; echo $a->button;';
      $button=$this->component->evaluateStatements($statements);
      $this->fail('exception not raised when evaluating an invalid statement');
    } catch(Exception $e) {
    }
  }
}

?>