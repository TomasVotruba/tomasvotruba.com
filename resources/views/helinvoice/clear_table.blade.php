@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <h1>Convert PDF invoice to Nice Clean Table</h1>

        <div class="row">
            <div class="col-6">

                <p class="mb-4">Pick PDF with your invoice and see what happens :)</p>

                <form
                    method="POST"
                    action="{{ action(\App\Http\Controllers\Helinvoice\ProcessInvoiceFormController::class) }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="form-group">
                        <input type="file" class="form-control" name="invoice_pdf" accept="application/pdf" required>
                    </div>

                    <br>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
