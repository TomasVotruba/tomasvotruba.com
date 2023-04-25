@php
    /** @var \App\ValueObject\CarReport[]|null $car_reports */
@endphp


@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <h1>Convert PDF invoice to Nice Clean Table</h1>

        <p class="text-secondary">Made for my Love, so she has more time for what matters the most to her (taking care
            of me :P)</p>

        <br>

        <div class="row">
            <div class="col-6 d-block">
                <p class="mb-4">Pick PDF with your invoice and see what happens :)</p>

                <form
                    method="POST"
                    action="{{ action(\App\Http\Controllers\InvoiceController::class) }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="form-group d-flex">
                        <input
                            type="file"
                            class="form-control me-3"
                            name="{{ \App\Enum\InputName::INVOICE_PDF }}"
                            accept="application/pdf"
                            required
                        >
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <br>
        <br>
        <br>

        @if ($car_reports !== null)
            <table class="table table-bordered table-responsive">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th class="text-center">Car Plate</th>
                        <th class="text-center">Date Period</th>
                        <th class="text-center">Volume</th>
                        <th class="text-center w-25">Total Price</th>
                        <th class="text-center w-25">After Discount</th>
                    </tr>
                </thead>

                @foreach ($car_reports as $car_report)
                    <tr>
                        <td class="text-end">
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                            {{ $car_report->getPlateId() }}
                        </td>
                        <td>
                            {{ $car_report->getDateRange() }}
                        </td>
                        <td class="text-end">
                            {{ nice_number($car_report->getTotalVolume()) }} l
                        </td>

                        <td class="text-end">
                            <strong>
                                {{ nice_number($car_report->getTotalPrice()) }} €
                            </strong>
                        </td>

                        <td class="text-end">
                            @if ($car_report->hasDiscounts())
                                {{ nice_number($car_report->getTotalPriceAfterDiscount()) }} €
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <th colspan="4" class="bg-gradient bg-black text-white" >Summary</th>

                    <th class="text-end">
                        {{ nice_number($invoice_total_price) }}
                        €
                    </th>
                    <td class="text-end">
                        {{ nice_number($invoice_total_price_after_discount) }}
                        €
                    </td>
                </tr>
            </table>
        @endif
    </div>
@endsection
