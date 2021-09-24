<?php

// https://botman.io

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

use App\Models\newBotmanQuestion;
use App\Models\botmanQuestion;
use App\Models\botmanAnswer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
// use Illuminate\Database\Eloquent\SoftDeletes;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */

    public function handle()
    {
        $botman = app('botman');

        $botman->fallback(function ($bot) {
            $message = $bot->getMessage();
            $bot->reply('Ich kann dich leider nicht verstehen: "'.$message->getText().'"' );
            if( strlen($message->getText()) > 3 )
             {
                 $botmanQuests = newBotmanQuestion::all();
                 $vorhanden=0;
                 foreach ($botmanQuests as $botmanQuest){
                    if ($message->getText() == $botmanQuest->question) {
                        $vorhanden=1;
                     }
                 }

                 if($vorhanden==0) {
                     $bot->reply('Ich lerne noch. Damit ich die nicht verstandene Frage im nachgang bearbeiten kann merke ich mir diese.');

                     if (auth()->guest()){
                         $userID=NULL;  // Als Gast angemelde
                         //$botmanuserid = $user->getId();
                         $chatUserName =    $bot->userStorage()->get('name');
                     }
                     else {
                         $userID=Auth::user()->id;
                         $botmanuserId=Null;
                     }

                     DB::table('new_botman_questions')
                         ->insert(
                             [
                                 array(
                                     'question'      => $message->getText(),
                                     'user_id'       => $userID,
                                     //'botmanuser_id' => $botmanuserId,
                                     'chatUserName'   => $chatUserName,
                                     'created_at'    => Carbon::now(),
                                     'updated_at'    => Carbon::now(),
                                 )
                             ]);
                 }
                 else{
                     $bot->reply('Die Frage ist schon im Arbeitsvorrat vorhanden.');
                 }
             }
            else {
                 $bot->reply('Die Frage war zu kurz um sie zu für die nachbearbeitung fest zu halten.');
             }
         }
        );

        $botman->hears('Hi', function($bot) {
            $this->askName($bot);
        });

        /*
        // Verhinder fas Fallbacksystem
        $botman->hears('{frage}' , function($bot , $frage) {
            if($frage =='Huhu') {
                $this->askName($bot);
            }
        });
        */

        $botman->hears('Hallo', function ($bot){
            $bot->reply('Wenn ich dich mit Namen anprechen soll sage: Mein Name ist (Name)');
        }
        );

        $botman->hears('Mein Name ist {name}', function ($bot, $name){
            $bot->userStorage()->save([
                'name' => $name
            ]);
            $bot->reply('Hallo ' . $name);
         }
        );

        $botman->hears('Sage mein Name' , function ($bot) {
           $name = $bot->userStorage()->get('name');
           if ($name=='') {
             $this->askName($bot);
           }
           else {
             $bot->reply('Dein Name ist ' . $name);
           }
         }
        );

        $botmanQuests = botmanQuestion::all();
        foreach($botmanQuests as $botmanQuest){
          $question=$botmanQuest->question;

          $botman->hears( $question , function ($bot) {
             $bot->reply('Hier würde Deine Antwort ausgegeben werden.');
           }
         );
        }

       $botman->listen();
    }

    /**
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $botman->ask('Ich kenne dein Namen nicht. Wenn ich dich mit deinen Namen anreden soll must du mir diesen mitteilen. Wie ist dein Name?'
            , function(Answer $answer) {

            $name = $answer->getText();
            if($name <> '') {
                $this->say('Schön, Sie kennenzulernen ' . $name);
              }
        });
    }

    public function store()
    {
        $name='test';
        $bot->userStorage()->save([
            'test' => $name
        ]);
    }

}
