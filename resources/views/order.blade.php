<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="row justify-content-center py-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Form</div>
                <div class="card-body">
                    <form action="" method="">
                        @csrf
                        <div class="form-group my-3">
                            <label for="">Item name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group my-3">
                            <label for="">Qty</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="form-group my-3">
                            <label for="">Price</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="form-group my-3">
                            <label for="">Grand total</label>
                            <input type="number" class="form-control">
                        </div>

                        <div class="form-group my-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>
