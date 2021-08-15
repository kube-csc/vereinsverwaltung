<?php
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
              $bot->reply('Ich lerne noch. Damit ich die nicht verstandene Frage im nachgang bearbeiten kann merke ich mir diese.');

                if (auth()->guest()){
                 $userID=NULL;  // Als Gast angemeldet
                 }
                else {
                  $userID=Auth::user()->id;
                }

                DB::table('new_botman_questions')
                 ->insert(
                   [
                    array(
                          'question'    => $message->getText(),
                          'user_id'    =>  $userID,
                          'created_at'  => Carbon::now(),
                          'updated_at'  => Carbon::now(),
                          )
                  ]);
              }
             else {
                 $bot->reply('Die Frage war kurz um sie zu für die nachbearbitung fest zu halten.');
             }
         }
       );


       $botmanQuests = botmanQuestion::all();
       foreach ($botmanQuests as $botmanQuest){
         $question=$botmanQuest->question;

          $botman->hears( $question , function ($bot) {
            $bot->reply('Gefunden');
           }
         );
        }

       $botman->listen();
    }

}
