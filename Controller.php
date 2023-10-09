<?php
declare(strict_types=1);

class YahtzeeController {
    
    private Yahtzee $model;
    private YahtzeeView $view;
    
  
    
    public function __construct(Yahtzee $model, YahtzeeView $view){
        $this->model = $model;
        $this->view = $view;
    }
    //d
    public function get_model() : Yahtzee {
        /*
        get_model() : YahtzeeModel
        > This method should return the model set by the constructor.
        */
        return $this->model;       
    }
    //d
    public function get_view() : YahtzeeView {
        /*
        get_view() : YahtzeeView
        > This method should return the view set by the constructor.
        */
        return $this->view;
    }
    // done
    public function get_possible_categories() : array {
        /*
        get_possible_categories() : array
        Returns an associative array.
        > The keys are a valid category on the score card (one that is
          currently NULL) and the values are what the score would be if the
          user chose that category, based on the model's "kept_dice".
        */
        $rv = array();
        $rv = $this->model->get_scorecard();
        foreach($rv as $key=>$value){
          if(is_null($rv[$key]) == false){
            unset($rv[$key]);
          }
        }
        // echo(print_r($rv, true));
        return $rv;
    }
    //not done
    public function process_score_input(string $line) : int {
        /*
        process_score_input(string: $line) : int
        Returns an integer that denotes the status of the user input for when
        they are choosing their score.
        > If the line given is "exit" or "q", return -1.
        - If an invalid category is given (meaning, one that is not in the 
          scorecard's keys, it should:
          > Update the first available category (NULL) to the score calculated
            from the model's kept_dice attribute.
          > Return -2, indicating an error occurred
        > If a valid category is given, it should update that category to the
          score calculated from the model's kept_dice attribute.
        */ 
    
        //how to stub model's kept_dice attribute
        // 

        $scorecardValues = $this->model->get_scorecard(); 
        $dice = $this->model->get_kept_dice();
        // echo(print_r($this->model->get_scorecard(), true));
        // echo("$line\n");
        if($line == "q" || $line == "exit"){
          return -1;
        }
        if(array_key_exists($line, $scorecardValues)){
          //  $this->model->roll(5);
         
          // echo(strval(array_sum($dice)));
          $this->model->update_scorecard($line, array_sum($dice));
          // echo(print_r($this->get_model()->get_scorecard(), true));
          // echo($line);
        }
        else{
          foreach($scorecardValues as $key=>$value){
            if(is_null($scorecardValues[$key])){
              $this->model->update_scorecard($key, array_sum($dice));
              break;
            }
          
          }
          // echo(print_r($this->model->get_scorecard(), true));
          return -2;
        }
        
        return 0;
    }
    
    public function process_keep_input(string $line) : int {
        /*
        process_keep_input(string $line) : int
        Processes the input from the user when determining which dice to keep.
        > If the line given is "exit" or "q", return -1.
        > If the line given is "all", immediately combine dice and return 0;
        > If the line given is "none" "pass" or blank "", return -2.
        - Otherwise, indexes are given
          > Attempt to call keep_by_index with the given line, return the int
            it returns, indicating remaining dice.
          > If keep_by_index throws an exception, catch it and return -2.
            (output the error message with the view).
        */
        
        if($line == "exit" || $line == "q"){
            return -1;
        }
        if($line == "" || $line == "none" || $line == "pass"){
            return -2;
        }
        if($line == "all"){
           $this->model->combine_dice();
           return 0;
        }
        try{
          $result =  $this->model->keep_by_index($line);
        }
        catch(Exception $result){
          $this->view->output(strval($result));
          return -2;
         }
          return $result;
      
       
    }
    
    public function handle_roll() : int {
        /* 
        handle_roll() : int
        This method rolls the dice, up to three times, and gathers input from
        the user on what to do with each roll.  This method should:
        - Enter a loop that runs twice, and set a variable to track
          $remaining_dice to 5.
        - Each loop:
          - Roll dice, the amount being whats in $remaining_dice. 
          - Take the user input, feed it into process_keep_input()
            > If the return value is -2, nothing happens, continue the loop.
            > If the return value is -1, return -1.
            > else, set the $remaining_dice local var to the return value.
          > If the $remaining_dice is 0, break out of the loop.
        > After the loop, if the model's kept_dice has less than 5 dice,
          roll a final time, and then combine dice the kept_dice should be 
          correct based on the rolls and inputs.
        > Return 0.
          
        Hint for this one: What you can test from the result of this method is
        that 1) the kept_dice is properly populated given a variety of
        scenarios (simulate different inputs) and 2) the return value is
        correct.  You'll want to stub the view to return pre-scripted
        responses, a good way to do this is a stub's onConsecutiveCalls method.
        */
      $remaining_dice = 5;
     
      for($i = 0; $i < 2; $i++){
       
        $this->model->roll($remaining_dice);
        $inputValue = $this->get_view()->get_user_input("test");
      //  echo(strval($remaining_dice));
        $result = $this->process_keep_input($inputValue);
        if($result == -2){}
        elseif($result == -1){
          return -1;
        }
        else{
          $remaining_dice = $result;
        }
        if($remaining_dice == 0){
          break;
        }
      }
      $diceNum = array_sum($this->model->get_kept_dice());
      if($diceNum < 5){
        $this->model->roll($diceNum);
        $this->model->combine_dice();
      }

        
        return 0;
    }
    
    public function main_loop() : int {
        /*
        main_loop() : int
        Runs the main loop of the controller.
        > The loop should run exactly 13 times, and the model's current_turn
        should be incremented each time.
        - (Output the current turn with the view)
        > If handle_roll returns -1, return -1.
        - (Output the kept dice after handle_roll with the view)
        - Get the valid categories calling get_possible_categories
        - (Output valid categories with view)
        - (Get user input with view)
        > Call process_score_input on the result from the user input.
          > If the returning values is -1, return -1.
        - (use the view to output the score)
        > At the end of each loop iteration, clear_kept_dice and increment_turn
        
        Hint: For this, you can inspect the model to see if behavior is being
        properly called/implemented.  You will also need to stub view to give
        pre-scripted inputs.  Remember, we primarily want to test data and
        behavior, NOT implementation details.
        */

        for($i = 0; $i < 13; $i++){
          $this->view->output_turn();
          $rollValue = $this->handle_roll();
          $this->view->output_kept_dice();
          if($rollValue == -1){
            return -1;
          }
          $availableCategories = $this->get_possible_categories();
          $this->view->output_array($availableCategories);
          $userInput = $this->get_view()->get_user_input("test");
          $keptInputValue = $this->process_keep_input("$userInput");
          if($keptInputValue == -1){
            return -1;
          }
          $this->view->output_score();
          $this->model->clear_kept_dice();
          $this->model->increment_turn();
        }
        
        
        return 0;
        
    }
}


?>