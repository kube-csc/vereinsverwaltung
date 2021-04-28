<?php
namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;

use App\Models\botmanQuestion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */

    public function handle()
    {
        $botman = app('botman');

        $botman->fallback(function ($bot) {
            $bot->reply('Ich kann dich leider nicht verstehn.');
        });

        $botman->hears('{message}', function($botman, $message) {

            if ($message <> '') {
                DB::table('botman_questions')
                 ->insert(
                   [
                    array(
                          'question'    => $message ,
                          'created_at'  => Carbon::now(),
                          'updated_at'  => Carbon::now(),
                          )
                  ]);
            }

        });

        $botman->hears('{message}', function($botman, $message) {

            if ($message  == 'Hi') {
              $this->askName($botman);
            }
        });

        $botman->hears('Hallo(.*)', function ($bot) {
            $bot->reply('Moin');
        });

        $botman->hears('Moin|Hy', function ($bot) {
            $bot->reply('Moin');
        });

        $botman->hears('I want ([0-9]+)', function ($bot, $number) {
         $bot->reply('You will get: '.$number);
         });

        $botman->listen();
    }

    /**
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $botman->ask('Veräts du mir dein Name? ', function(Answer $answer) {

          $name = $answer->getText();

          if ($name <> 'nein')

          {
            $this->say('Wie kann ich Dir helfen? '.$name);
           }
          else
          {
            $this->say('Ok dann nenne ich dich Kanute.');
          }
        });
    }

    public function askParty($botman)
    {
        $botman->ask('Welche Party soll Starten', function(Answer $answer) {

            $ort = $answer->getText();

            $this->say('Geh nach '.$ort.'?');
        });
    }

}

/*
$botmanQuestion= new botmanQuestion(
  [
    'question'=>$message
  ]
  );
  $botmanQuestion->save();

  DB::table('botman_questions')
   ->insert(
     [
      array(
            'question'    => $message,
            'autor'       => auth()->user()->id,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
            )
    ]);
