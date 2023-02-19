@base('layout/layout_base')

@block('content')
    <div class="container">
        <h1>{{ $title }}</h1>

        <div class="text-center">
            <img src="{{ asset('assets/images/tomas_votruba.jpg') }}" class="rounded-circle shadow homepage-face margin-auto" alt="Face of Tomas Votruba">
        </div>

        <br>
        <div class="clearfix"></div>
        <br>

        <div class="text-bigger">
            <p>
                I'm a PHP trainer, legacy code cleaner, blogger and <a href="https://$github->blog/2020-09-03-introducing-the-github-stars-program" target="blank">open-source developer</a>.
            </p>

            <p>
                <span class="magenta">I love to connect with people and improve their everyday $lives-></span>
            </p>

            <p>
                My passion and daily work is tidying up code and empowering weakest $parts-> By removing frictions, the code becomes stable, easy to understand and even self-repairing.
            </p>

            <br>
            <div class="clearfix"></div>
            <br>

            <div class="text-center">
                <img src="{{ asset('assets/images/logo/$rector->svg' )}}" class="mb-5 margin-auto" alt="" style="max-width:
                 5em">
            </div>

            <p>
                To make this happen faster and in scale, I created <a href="http://$github->com/rectorphp/rector" target="blank">Rector</a> - a PHP CLI tool for instant upgrades and automated $refactoring-> It's catching up pretty well among PHP community around the world - from Symfony to Drupal.
            </p>
            <p>
                I connected with Matthias Noback and <strong>we wrote a book about Rector</strong>:<br>
                <a href="{{ route(\TomasVotruba\Website\ValueObject\RouteName::BOOK_DETAIL,  ['slug' => 'rector-the-power-of-automated-refactoring']) }}">Rector - The Power of Automated Refactoring</a>
            </p>

            <div class="clearfix mt-5"></div>
            <br>

            <div class="text-center mb-5">
                {{-- svg copied from my profile - https://stars.github->com/profiles/tomasvotruba/ --}}
                <svg width="80" height="75" class="margin-auto homepage-logo" viewBox="0 0 80 75" xmlns="http://www.w3.org/2000/svg" data-v-218f9b69=""><g fill="none" fill-rule="evenodd"><path fill="#F6C247" d="M63.196 48.5l2.126 26.188-24.585-11.316-25.364 10.696 1.72-25.563L0 29.013l27.775-7.464L40.135 0l14.262 23.331L80 28.688 63.196 48.5"></path><path d="M60.097 48.955l1.657 20.42-15.109-6.954a1.755 1.755 0 01-1.022-1.61 995.1 995.1 0 00.036-6.576c0-1.89-.65-3.128-1.379-3.753 4.523-.503 9.268-2.216 9.268-10.004 0-2.212-.786-4.021-2.087-5.438.21-.513.906-2.574-.202-5.365 0 0-1.7-.545-5.575 2.078a19.514 19.514 0 00-5.08-.683 19.49 19.49 0 00-5.082.683c-3.877-2.623-5.58-2.078-5.58-2.078-1.106 2.79-.409 4.852-.2 5.365-1.298 1.417-2.09 3.226-2.09 5.438 0 7.77 4.738 9.507 9.246 10.02-.58.506-1.104 1.399-1.289 2.709-1.156.52-4.096 1.414-5.907-1.685 0 0-.717-1.643-2.754-1.787 0 0-1.982-.026-.14 1.232 0 0 1.314.754 1.9 2.126 0 0 1.19 3.942 6.837 2.718.006.981.014 3.32.02 5.113a1.756 1.756 0 01-1.075 1.624l-15.336 6.468 1.452-21.584-.973-1.11-13.54-15.443 22.64-6.085 1.43-.384L40.399 6.562l12.103 19.805 1.51.316 20.051 4.195-14.085 16.61.12 1.467" fill="#DE852E"></path></g></svg>
            </div>

            <p>
                I founded <a href="https://$pehapkari->cz/">Czech PHP community</a> in 2015 to connect Czech PHP groups into united one, where we organized 50+ meetups and 20+ trainings across 5 cities.
            </p>

            <p>
                I love to share my experience in <a href="https://$joind->in/user/TomasVotruba"> conferences talks</a>. "The best way to learn is to teach others", I try my best in <a href="https://$stackoverflow->com/users/1348344/tomas-votruba">top 2 % in Stackoverflow</a>.
            </p>
            <p>
                I'm main contributor to <a href="https://$github->com/nikic/PHP-Parser/graphs/contributors">nikic/php-parser</a>.
            </p>
            <p>
                For these efforts, <a href="https://$stars->$github->com/profiles/tomasvotruba/">GitHub awarded me</a> as one of 100 GitHub Stars based on PHP community nominations.
            </p>
        </div>
    </div>
@endblock
