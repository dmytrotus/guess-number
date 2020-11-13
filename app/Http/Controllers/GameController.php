<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Facades\GamesService;

class GameController extends Controller
{
    public function index(){

    	$games = Game::all();

    	return response()->json([
            'success' => true,
            'message' => $games

        ], 200);
    }

    public function newGame( Request $r ){

    	$gameData = GamesService::createGame( $r );

    	if($gameData['httpStatus'] > 226)
    	{
    		return response()->json([
	            'message' => $gameData['message']

	        ], $gameData['httpStatus']);
    	}

    	return response()->json([
            'id' => $gameData['id']

        ], 200);
    }

    public function guess( Request $r ){

    	$gameData = GamesService::guess( $r );

    	if($gameData['httpStatus'] > 226)
    	{
    		return response()->json([
	            'message' => $gameData['message']

	        ], $gameData['httpStatus']);
    	}

    	return response()->json([
            'status' => $gameData['status'],
            'number' => $gameData['number'],
            'score'  =>  $gameData['score'],
            'place'  => $gameData['place']

        ], 200);
    	
    }

    public function scores(){
    	
    	$games = Game::orderBy('coefficient')->take(30)->get();

    	foreach ($games as $game) {
    		$scores[] = [
    			'playerName' => $game->playerName,
    			'score' => $game->coefficient,
    			'place' => GamesService::getPlace($game->id)
    		];
    	}


    	return response()->json([
            'success' => true,
            'message' => $scores

        ], 200);
    }
}
