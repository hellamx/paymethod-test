<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Payment method test</title>
    </head>
    <body>
        <div class="wrapper">
            <div class="card">
                <h1>Payment by card</h1>
                <form method="post" action="{{ route('payment.card') }}">
                    @csrf
                    <input type="submit" value="Pay">
                </form>
            </div>
            <div class="sbp">
                <h1>Payment by sbp</h1>
                <form method="post" action="{{ route('payment.sbp') }}">
                    @csrf
                    <input type="submit" value="Pay">
                </form>
            </div>
            <div class="sbp">
                <h1>Payment by installment (plate)</h1>
                <form method="post" action="{{ route('payment.plate') }}">
                    @csrf
                    <input type="submit" value="Pay">
                </form>
            </div>
        </div>
    </body>
</html>
