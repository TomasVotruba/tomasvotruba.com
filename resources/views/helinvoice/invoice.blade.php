@php
    /** @var \App\ValueObject\FuelInvoice|null $fuel_invoice */
@endphp


@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <h1>Convert PDF Invoice to Clean Table 🧼️️</h1>

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

        @if ($fuel_invoice instanceof \App\ValueObject\FuelInvoice)
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

                @foreach ($fuel_invoice->getCarReports() as $car_report)
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

                <tr
                    @class([
                        'bg-success-subtle' => $fuel_invoice->areTotalPricesMatching(),
                        'bg-error-subtle' => ! $fuel_invoice->areTotalPricesMatching(),
                        'text-black-50'
                    ])
                >
                    <th colspan="5">Summary Check</th>

                    <td class="text-end">
                        <strong>
                            {{ nice_number($fuel_invoice->getCarReportsTotalPriceAfterDiscount()) }} €
                        </strong>
                    </td>
                </tr>
            </table>

            <br>
            <br>

            @if ($fuel_invoice->areTotalPricesMatching())
                <div class="card bg-success text-white border-5">
                    <div class="card-body">
                        <div style="font-size: 3rem" class="float-end mt-3 me-2">🥳️</div>

                        <p>
                            The table records <strong>total price MATCHES</strong> the invoice total:
                            <strong>{{ nice_number($fuel_invoice->getTotalPriceAfterDiscount()) }} €</strong>
                        </p>

                        <p>
                            Good job!
                        </p>

                    </div>
                </div>
            @else
                <div class="card bg-danger text-white border-5">
                    <div class="card-body">
                        <div style="font-size: 3rem" class="float-end mt-3 me-2">😿️</div>

                        <p>
                            The table records <strong>total price DOES NOT match</strong> the invoice total:
                            <strong>{{ nice_number($fuel_invoice->getTotalPriceAfterDiscount()) }} €</strong>
                        </p>

                        <p>
                            Nooooooo!
                        </p>

                    </div>
                </div>
            @endif

            <br>
            <br>

            <p class="text-secondary">❤ Made for my Love️️, so she has more time for what matters the most to her... (taking care
                of me 😸)</p>
        @endif
    </div>
@endsection
