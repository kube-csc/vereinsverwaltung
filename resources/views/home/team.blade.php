<!-- ======= Team Section ======= -->
<section id="team" class="team">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Team</h2>
            <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem.
            </p>
        </div>

        <div class="row">
            @php
            $delay=100;
            $delayname ="data-aos-delay=\"".$delay."\"";
            @endphp
            @foreach($boards as $board)
            <div class="col-lg-3 col-md-6" >
                <div class="member" data-aos="fade-up" {{$delay == 100 ? '' : 'data-aos-delay='.$delay}}>
                    <div class="pic">
                    @if(isset($board->vorstandsbild))
                       <img src="storage/team/{{ $board->vorstandsbild }}" class="img-fluid" alt="">
                    @else
                       <img src="asset/img/teamleer.jpg" class="img-fluid" alt="">
                    @endif
                    </div>
                    <div class="member-info">
                        <h4>{{ $board->vorname }} {{ $board->nachname }}</h4>
                        @if($board->geschlecht=='m')
                            <span>{{ $board->postenmaenlich }}</span>
                        @else
                            <span>{{ $board->postenweiblich }}</span>
                        @endif
                        <div class="social">
                            @php /*
                            <a href=""><i class="icofont-twitter"></i></a>
                            <a href=""><i class="icofont-facebook"></i></a>
                            <a href=""><i class="icofont-instagram"></i></a>
                            <a href=""><i class="icofont-linkedin"></i></a>
                            */ @endphp
                            <a href=""><i class="icofont-mail"></i>{{ $board->vorstandsemail }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $delay=$delay+50;
            @endphp
            @endforeach

        </div>

    </div>
</section><!-- End Team Section -->
