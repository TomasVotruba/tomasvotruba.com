@php
    /** @var \App\ValueObject\FuelInvoice|null $fuel_invoice */
@endphp

@extends('layout/layout_base')

@section('wide_content')
    <div class="container" style="max-width: 80rem">
        <h1>Convert PDF Invoice to Clean Table 🧼️️</h1>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-2">1. Pick PDF Invoice File from your computer ↓ 😹</p>

                        <form
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            <div class="form-group">
                                <input
                                    type="file"
                                    class="form-control me-3"
                                    name="{{ \App\Enum\InputName::INVOICE_PDF }}"
                                    accept="application/pdf"
                                    required
                                >
                            </div>

                            <div class="mt-4">
                                <p class="mb-2">2. Upload it ↓</p>
                                <button type="submit" class="btn btn-success">Submit PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <br>

        @if ($fuel_invoice instanceof \App\ValueObject\FuelInvoice)
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h3 class="mt-1 mb-1">Invoice no. {{ $fuel_invoice->getInvoiceNumber() }}</h3>
                </div>
                <div class="card-body text-center">
                    <div class="row ">
                        <div class="col-3">
                            Date: <strong>{{ $fuel_invoice->getInvoiceDate() }}</strong>
                        </div>
                        <div class="col-3">
                            Ordine: <strong>4222131633</strong>
                        </div>

                        <div class="col-6">
                            <p>
                                Table records match invoice base?
                                @if ($fuel_invoice->areBasePriceMatching())
                                    <span class="text-success"><strong>Yes 🥳</strong></span>
                                @else
                                    😿
                                    !== {{ nice_number($fuel_invoice->getTotalBase()) }}&nbsp;€
                                @endif
                            </p>

                            <p>
                                Table records match invoice tax?
                                @if ($fuel_invoice->areTaxPriceMatching())
                                    <span class="text-success"><strong>Yes 🥳</strong></span>
                                @else
                                    😿
                                    !== {{ nice_number($fuel_invoice->getTotalTax()) }}&nbsp;€
                                @endif
                            </p>

                            <p>
                                Table records match invoice total?
                                @if ($fuel_invoice->areTotalPricesMatching())
                                    <span class="text-success"><strong>Yes 🥳</strong></span>
                                @else
                                    😿
                                    !== {{ nice_number($fuel_invoice->getTotalPrice()) }}&nbsp;€
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

{{--            @importo - total price with tax--}}

            <table class="table table-bordered table-responsive table-striped">
                <thead class="table-dark">
                    <tr class="text-center align-middle">
                        <th>#</th>
                        <th>Car Plate</th>
                        <th>Driver</th>
                        <th>Date</th>
                        <th>Base Price</th>
                        <th>Tax<br>(22 %)</th>
                        <th>Price with Tax</th>
                        <th>FB<br>(40 %)</th>
                        <th>FD<br>(60 %)</th>
                    </tr>
                </thead>

                @foreach ($fuel_invoice->getCarReports() as $car_report)
                    <tr>
                        <td class="text-end">
                            {{ $loop->index + 1 }}
                        </td>
                        <td style="white-space: nowrap">
                            {{ $car_report->getReadablePlateId() }}
                            <br>
                            <span class="text-secondary">
                                Telepass
                            </span>
                        </td>
                        <td style="font-size: .8rem">
                            {{ $car_report->getDriverName() }}

                            <br>

                            <span class="text-secondary">
                                {{ $car_report->getCarName() }}
                            </span>
                        </td>

                        <td style="font-size: .8rem">
                            {{ $car_report->getDateRange() }}
                        </td>

                        <td class="text-end">
                            {{ nice_number($car_report->getBasePrice()) }}&nbsp;€
                        </td>

                        <td class="text-end">
                            {{ nice_number($car_report->getTax()) }} €
                        </td>

                        <td class="text-end">
                            <strong>
                                {{ nice_number($car_report->getTotalPrice()) }}&nbsp;€
                            </strong>
                        </td>

                        <td class="text-end">
                            {{ nice_number($car_report->getFB()) }}&nbsp;€
                        </td>

                        <td class="text-end">
                            {{ nice_number($car_report->getFD()) }}&nbsp;€
                        </td>
                    </tr>
                @endforeach

                <tr
                    @class([
                        'bg-warning-subtle',
                        'text-black-50',
                    ])
                >
                    <th colspan="4">Table Records Cross-Check</th>

                    <td class="text-end nobr">
                        {{ nice_number($fuel_invoice->getCarReportsBasePriceTotal()) }}&nbsp;€
                    </td>

                    <td class="text-end nobr">
                        {{ nice_number($fuel_invoice->getCarReportsTaxTotal()) }}&nbsp;€
                    </td>

                    <td class="text-end nobr">
                        <strong>
                            {{ nice_number($fuel_invoice->getCarReportsTotalPrice()) }}&nbsp;€
                        </strong>
                    </td>

                    <td colspan="2"></td>
                </tr>

                <tr class="bg-info-subtle text-black-50 text-end nobr">
                    <th colspan="4" class="text-start">Invoice Totals Cross-Check</th>

                    <td>
                        {{ nice_number($fuel_invoice->getTotalBase()) }} €
                    </td>

                    <td>
                        {{ nice_number($fuel_invoice->getTotalTax()) }} €
                    </td>

                    <td>
                        <strong>
                            {{ nice_number($fuel_invoice->getTotalPrice()) }} €
                        </strong>
                    </td>

                    <td colspan="2"></td>
                </tr>
            </table>

            <br>
            <br>

            <p class="text-secondary">❤ Made for my Love️️, so she has more time for what matters the most to her...
                (taking care
                of me 😸)</p>
        @else
            <p>
                Your helpful table will ge generated here.
            </p>
        @endif
    </div>
@endsection
