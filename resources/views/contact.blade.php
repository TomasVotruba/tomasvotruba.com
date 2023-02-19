@extends('layout/layout_base')

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>

        <h2>Fast Check with Call</h2>

        <h3>
            <a href="tel:+420776778332">+420 <strong>776 778 332</strong></a>
        </h3>

        <br>

        <p>
            3 minutes of real-time chat beats 10-emails ping-pong spread across couple of weeks.
        </p>
        <p>
            Do you want to <strong>get work done</strong>, <strong>ask a question</strong> or have a friendly chat? Call me.
            <br>
            I pick up or reply the moment I'm available.
        </p>

        <hr class="mt-5">

        <h2>Take Your time with Email</h2>
        <h3>
            <a href="mailto:tomas.vot@gmail.com">tomas.vot@gmail.com</a>
        </h3>

        <br>

        <p>
            Can you wait a week for reply or are you in very different time-zone? Do you want to send me documents?<br>
            For these cases, please use an email.
        </p>

        <br>

        <hr>

        <br>
        <br>
        <br>

        <div class="text-secondary">
            <h3 id="bank">Financial Connection</h3>

            <div class="row">
                <div class="col-4">
                    <p>
                        <strong>Czech</strong>
                    </p>
                    <p>
                        FIO:<br>
                        2401087791/2010
                    </p>
                </div>

                <div class="col-4">
                    <p>
                        <strong>International</strong>
                    </p>
                    <p>
                        IBAN:<br>CZ4620100000002401087791
                    </p>
                    <p>
                        SWIFT/BIC:<br>FIOBCZPPXXX
                    </p>
                </div>

                <div class="col-4">
                    <p>
                        <strong>Registration</strong>
                    </p>
                    Registered at Licensing Authority Liberec under <a href="https://rejstrik.penize.cz/ares/01241451-tomas-votruba">ICO 01241451</a>
                </div>
            </div>
        </div>
    </div>
@endsection
