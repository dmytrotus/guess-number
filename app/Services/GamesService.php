<?php

namespace App\Services;
use App\Models\Game;

class GamesService
{
	public function calculateCoef( $gameId )
	{	
		///the less coeficient is better for the gamer.
		$game = Game::find($gameId);
		if($game == null){
			return 'The game is not exists';
		}

		$goal_coef = $game->done_attempts * $game->correct_number;
		$gamer_coef = array_sum($game->numbers);
		$coef = abs ( $goal_coef - $gamer_coef ) ; // how close was the user to correct answer throught all attempts

		return $coef;
	}


	public function getPlace( $gameId )
	{	
		$in_order = Game::orderBy('coefficient')->pluck('id')->toArray();
		$index_of_this_game = array_search($gameId, $in_order);
		$place = $index_of_this_game + 1;

		return $place;
	}


	public function createGame( $r )
	{
		$default_playerName = 'Unnamed player';
    	$default_from = 1;
    	$default_to = 3;
    	$default_attempts = 3;

    	///"from" "to" difference if interval is less than 3
    	if($r->to - $r->from < 3){
    		return [
		            'message' => 'Please change from to numbers',
		            'httpStatus' => 400
		        ];
    	}


    	$newGame = Game::create([
    		'playerName' => $r->playerName ?? $default_playerName,
    		'from' 		 => $r->from ?? $default_from,
    		'to' 		 => $r->to ?? $default_to,
    		'attempts' 	 => $r->attempts ?? $default_attempts
    	]);

    	$correct_number = rand($newGame->from, $newGame->to);
    	$newGame->correct_number = $correct_number;
    	$newGame->save();

    	return ['id' => $newGame->id, 'httpStatus' => 201 ];
	}

	public function guess( $r )
	{	
		//validate data
		foreach ($r->all() as $variable) {
			if(!is_numeric($variable) || $variable <= 0){
				return [
		            'message' => 'Id and number need to be positive numbers',
		            'httpStatus' => 400,
		        ];
			}
		}

		$game = Game::find($r->id);

		if($game == null){
			return [
		            'message' => 'The game with this id doest exists',
		            'httpStatus' => 400
		        ];
		}


		///if status won
		if(in_array($game->status, ['won', 'lost'])){
			return [
				'status' => $game->status,
	            'number' => $r->number,
	            'score' => $game->coefficient,
	            'place' => $this->getPlace( $game->id ),
	            'httpStatus' => 200
			];
		}

		///play the game

		//check attempts
		if($game->done_attempts >= $game->attempts){
			$game->status = 'lost';
			$game->save();

			return [
	            'status' => $game->status,
	            'number' => $r->number,
	            'score' => $game->coefficient,
	            'place' => $this->getPlace( $game->id ),
	            'httpStatus' => 200
	        ];
		}

		$game->status = 'pending';
		$numbers = $game->numbers;
		//add numbers to array
		if($numbers == null){
			$numbers[] = $r->number;
		} else {
			$numbers = $game->numbers;
			$numbers[] = $r->number;
		}
		$game->numbers = $numbers;
		$game->save();

		$game->coefficient = $this->calculateCoef( $game->id );
		$game->save();

		//if guess the number
		if($game->correct_number == $r->number){
			$game->status = 'won';
			$game->save();
		}
		$game->done_attempts = $game->done_attempts + 1;
		$game->save();

		//check attempts
		if($game->done_attempts >= $game->attempts && $game->correct_number != $r->number){
			$game->status = 'lost';
			$game->save();

			return [
	            'status' => $game->status,
	            'number' => $r->number,
	            'score' => $game->coefficient,
	            'place' => $this->getPlace( $game->id ),
	            'httpStatus' => 200
	        ];
		}


		return [
	            'status' => $game->status,
	            'number' => $r->number,
	            'score' => $game->coefficient,
	            'place' => $this->getPlace( $game->id ),
	            'httpStatus' => 200
	        ];

	}
}