@php
    $full_url = \URL::full();
    $full_url=str_replace('https://', '', $full_url);
    $full_url=str_replace('http://', '', $full_url);
    $full_url=str_replace('www.', '', $full_url);
    $full_url=str_replace('%3F', '?', $full_url);
    $full_url=str_replace('%3D', '=', $full_url);
    $full_url=str_replace('%26', '&', $full_url);
    $full_url=str_replace('%2F', '/', $full_url);
    $full_url=str_replace('%20', ' ', $full_url);
    $full_url=str_replace('%25', ' ', $full_url);
    $full_url='www.'.$full_url;
    $ip = getenv ("REMOTE_ADDR");

    if($ip != 1){
         $backlink = Illuminate\Support\Facades\DB::table('backlinks')
         ->where('backlink', $full_url)
         ->where('visible', 1)
         ->where('teilUrl' , '0')
         ->first();

         if(isset($backlink->neueUrl)){
                 $newURL=$backlink->neueUrl;
                 $updateWeiterleitungID=$backlink->id;
                 $weiterleitAnzahl=$backlink->weiterleitAnzahl;
         }

         if(!isset($backlink->neueUrl)){
             $link = explode('?',$full_url);
             $i=0;
             foreach($link as $velue){
                 if($i!=0){
                    $link[1]=$link[1].'&'.$velue;
                 }
                 ++$i;
             }
             $linkCount=count($link);

             // Backlink wird ohne Parameter bearbeitet
             $backlink = Illuminate\Support\Facades\DB::table('backlinks')
             ->where('backlink', 'like' , '%'.$link[0].'%')
             ->where('teilUrl' , '1')
             ->where('visible', 1)
             ->first();

             if(isset($backlink->neueUrl)){
                 $newURL=$backlink->neueUrl;
                 $updateWeiterleitungID=$backlink->id;
                 $weiterleitAnzahl=$backlink->weiterleitAnzahl;
             }

             //Ein Parameter wird vom Backlink als Parameter benötigt
             if(!isset($backlink) && $linkCount>1){
                 $linkParameterliste = explode('&',$link[1]);
                 $linkParameter[0] = explode('=',$linkParameterliste[0]);
                 if(count($linkParameter[0])>1){
                   $backlink = Illuminate\Support\Facades\DB::table('backlinks')
                     ->where('backlink', 'like' , '%'.$link[0].'%')
                     ->where('backlink', 'like' , '%'.$linkParameter[0][0].'%')
                     ->where('teilUrl' , '2')
                     ->where('visible', 1)
                     ->first();

                       if(isset($backlink->neueUrl)){
                          $newURL=$backlink->neueUrl.$linkParameter[0][1];
                          $updateWeiterleitungID=$backlink->id;
                          $weiterleitAnzahl=$backlink->weiterleitAnzahl;
                       }
                      if(!isset($newURL)){
                         $backlinkTests = Illuminate\Support\Facades\DB::table('backlinks')
                             ->where('backlink', 'like' , '%'.$link[0].'%')
                             ->where('teilUrl' , '4')
                             ->where('visible', 1)
                             ->get();

                         foreach($backlinkTests as $backlinkTest){
                           foreach($linkParameterliste as $value){
                              $linkParameter = explode('=',$value);
                              if($backlinkTest->prefixName == $linkParameter[0]){
                                 $newURL=$backlinkTest->neueUrl.$linkParameter[1];
                                 $updateWeiterleitungID=$backlinkTest->id;
                                 $weiterleitAnzahl=$backlinkTest->weiterleitAnzahl;
                                 break;
                              }
                           }
                         }
                      }
                 }
             }
         }

         if(!isset($backlink)){
               // Backlink wird ein Word ersetzt
             if ($linkCount>1){
                 $backlink = Illuminate\Support\Facades\DB::table('backlinks')
                 ->where('backlink', 'like' , '%'.$link[0].'%')
                 ->where('teilUrl' , '3')
                 ->where('visible', 1)
                 ->first();
             }
             else
             {
                 $linkParameter = explode('/',$link[0]);
                 $linkParameterCount = count($linkParameter)-1;

                 if($linkParameterCount>1){
                   $i=0;
                   $linksErsetzen='';
                   foreach($linkParameter as $value){
                       ++$i;
                       if ($linkParameterCount>$i){
                        $linksErsetzen=$linksErsetzen.'/'.$linkParameter[$i];
                       }
                   }

                   $backlink = Illuminate\Support\Facades\DB::table('backlinks')
                   ->where('backlink', 'like' , '%'.$linksErsetzen.'%')
                   ->where('teilUrl' , '3')
                   ->where('visible', 1)
                   ->first();
                 }
             }

                 if(isset($backlink->prefixName)){
                   $newURL=str_replace($backlink->prefixName , $backlink->neueUrl , $full_url);
                   $updateWeiterleitungID=$backlink->id;
                   $weiterleitAnzahl=$backlink->weiterleitAnzahl;
                  }
         }

         // Keine erneute aufnahme in der Datenbank
         $updateId=0;
         if(!isset($newURL)){
           $backlink = Illuminate\Support\Facades\DB::table('backlinks')
                     ->where('backlink', $full_url)
                     ->where('neueUrl' , Null)
                     ->where('prefixName' , Null)
                     ->first();

               if($backlink == Null){
                   $prefixs = Illuminate\Support\Facades\DB::table('backlinks')
                        ->where('teilUrl' , '5')->get();

                   foreach($prefixs as $prefix){
                        if(strpos($full_url , $prefix->prefixName) !== false){
                            $updateId=$prefix->id;
                            Illuminate\Support\Facades\DB::table('backlinks')
                              ->where('id' , $updateId)
                              ->update([
                                'nichtgefundenAnzahl' => $prefix->nichtgefundenAnzahl+1,
                                'nichtgefundenDatum'  => Illuminate\Support\Carbon::now()
                             ]);
                          break;
                        }
                   }
               }
               else
               {
                   $updateId=$backlink->id;
                   $notFoundCount=$backlink->nichtgefundenAnzahl+1;
                   Illuminate\Support\Facades\DB::table('backlinks')
                      ->where('id' , $updateId)
                      ->update([
                        'nichtgefundenAnzahl'    => $backlink->nichtgefundenAnzahl+1,
                        'nichtgefundenDatum'  => Illuminate\Support\Carbon::now()
                     ]);
               }

            if($updateId==0){
                   Illuminate\Support\Facades\DB::table('backlinks')->insert([
                    'ip'                  => $ip,
                    'backlink'            => $full_url,
                    'visible'             => '1',
                    'nichtgefundenAnzahl' => 1,
                    'nichtgefundenDatum'  => Illuminate\Support\Carbon::now(),
                    'created_at'          => Illuminate\Support\Carbon::now(),
                    'updated_at'          => Illuminate\Support\Carbon::now(),
                 ]);
            }
            else{
                  Illuminate\Support\Facades\DB::table('backlinks')
                      ->where('id' , $updateId)
                      ->update([
                        'nichtgefundenAnzahl' => $notFoundCount,
                        'nichtgefundenDatum'  => Illuminate\Support\Carbon::now()
                     ]);
                 }
         }

         if(isset($newURL)){
              Illuminate\Support\Facades\DB::table('backlinks')
                 ->where('id' , $updateWeiterleitungID)
                 ->update([
                   'weiterleitAnzahl' => ++$weiterleitAnzahl,
                   'weiterleitDatum'  => Illuminate\Support\Carbon::now()
                ]);

                $url='Location: '.env('APP_HTTP').$newURL;
                header('HTTP/1.1 301 Moved Permanently');
                header($url);
            exit();
         }
    }
@endphp

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="canonical" href="{{env('APP_URL')}}" />
    <title>404</title>
</head>
<body style="background: url('/asset/img/error-image.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center">

<div style='color: white;margin: 100px auto;font-family: "Nunito", sans-serif; text-align: center'>
    <h1>Oops !!!<br><br><span style="font-size: 300%;">404</span><br><br></h1>
    <p>Die gesuchte Seite wurde nicht gefunden.</p>
    <br><br>
    <p class="text-light" style="font-size: 300%;">
        <a href="{{env('APP_URL')}}">{{ str_replace('_' , ' ' , env('Verein_Domain')) }}</a>
    </p>
</div>

</body>
</html>
