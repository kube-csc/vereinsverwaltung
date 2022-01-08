@php
    $full_url = \URL::full();
    $full_url=str_replace('https://', '', $full_url);
    $full_url=str_replace('http://', '', $full_url);
    $ip = getenv ("REMOTE_ADDR");
    if($ip != 1){
         $backlink = Illuminate\Support\Facades\DB::table('backlinks')
         ->where('backlink', $full_url)
         ->where('visible', 1)
         ->first();

         if($backlink ==  Null){
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
             if($backlink->neueUrl == Null){
                    Illuminate\Support\Facades\DB::table('backlinks')
                      ->where('id', $backlink->id)
                      ->update([
                        'nichtgefundenAnzahl' => ++$backlink->nichtgefundenAnzahl,
                        'nichtgefundenDatum'  => Illuminate\Support\Carbon::now()
                     ]);
             }
         }

         if(isset($backlink->neueUrl)){
             Illuminate\Support\Facades\DB::table('backlinks')
                  ->where('id', $backlink->id)
                  ->update([
                    'weiterleitAnzahl' => ++$backlink->weiterleitAnzahl,
                    'weiterleitDatum'  => Illuminate\Support\Carbon::now()
                 ]);

            $url='Location: '.$backlink->neueUrl;
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
    <!-- <meta http-equiv="refresh" content="10 ; URL=https://www.kel-datteln.de"/> -->
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
