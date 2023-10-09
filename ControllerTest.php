<?php
require_once 'Main.php';
/* Comment added to test GIT */
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    private $model;
    private $view;
    private $sut;
    
    public function setUp() :void {
        $d = new YahtzeeDice();
        $this->model = new Yahtzee($d);
        $this->view = $this->createStub(YahtzeeView::class);
        $this->sut = new YahtzeeController($this->model, $this->view);
    } 
    /**
    * @covers \YahtzeeController::get_model
    */
    public function test_get_model(){
        $result = $this->sut->get_model();
        $this->assertNotNull($result);
    }
    /**
    * @covers::get_view
    */
    public function test_get_view(){
        $result = $this->sut->get_view();
        $this->assertNotNull($result);
    }

    /**
    * @covers \YahtzeeController::main_loop
        */
    public function test_main_loop(){
        $result = $this->sut->main_loop();
        $this->assertEquals($result, 0);
    }
      /**
    * @covers \YahtzeeController::main_loop
        */
    public function test_main_loop_roll_q(){
        $stub = $this->view;
        $stub->method('get_user_input')->willReturn("q");
        $result = $this->sut->main_loop();
        $this->assertEquals($result, -1);
    }
    /**
    * @covers \YahtzeeController::main_loop
    */
    public function test_main_loop_user_input_q(){
        $stub = $this->view;
        $stub->method('get_user_input')->willReturnOnConsecutiveCalls("all","q");
        $result = $this->sut->main_loop();
        $this->assertEquals($result, -1);
    }
    
    }
