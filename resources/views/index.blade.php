<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> 
    <title>Alireview App</title>
</head>
<body class="bg-white">
   <div class="container bg-body p-3">
    <div class="d-flex flex-column align-items-center mb-5">
        <h2>AliReview</h2>
        <div class="d-flex w-100">
            @csrf
            <div class="form flex-grow-1 me-3">
                <div class="form-group mb-3">
                    <input type="text" name="shopName" class="form-control" placeholder="Shop name (shop-name.myshopify.com ... )" required>
                </div>
                <div class="form-group">
                    <input type="text" name="accessToken" class="form-control" placeholder="Access token ..." required>
                </div>
            </div>
            <button class="btn-success px-3 rounded" id="crawBtn">Craw</button>
        </div>
    </div>
    <div id="message" class="mb-5 text-center fs-3">

    </div>
    <div id="table-content">
        Loading...
    </div>
   </div>
</body>
<script>
$(document).ready(function(){
    $("#table-content").load("{{ route('apps.list') }}");

    $('#crawBtn').click(function (e) { 
            let shopName = $('input[name=shopName]').val();
            let accessToken = $('input[name=accessToken]').val();
            e.preventDefault();
            var convert_seconds_to_ms = (seconds) => {
                let m = Math.floor(seconds / 60);
                let s = seconds - m * 60;
                return m + " : " + s;
            };
            var second = 1;
            var intervalId = setInterval(() => {
                $("#message").html("Crawling... It'll take a long time...! ( " + convert_seconds_to_ms(second++) + " )");
            }, 1000);
            $.ajax({
                type: "POST",
                url: "{{ route('craw') }}",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'shopName': shopName,
                    'accessToken': accessToken
                },
                dataType: "json",
                success: function (response) {
                    $("#message").html('');
                    clearInterval(intervalId);
                    if(response['type'] == 'error'){
                        toastr.error(response['message']);
                    }else{
                        toastr.success(response['message']);
                        $("#table-content").html("Loading...");
                        $("#table-content").load("{{ route('apps.list') }}");
                    }
                }
            });
    });
});
</script>
</html>