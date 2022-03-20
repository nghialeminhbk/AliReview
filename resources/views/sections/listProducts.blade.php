<div class="mb-3 d-flex align-items-center">
    <button class="btn btn-info me-3" id="back"><<</button> <span class="fw-bold">{{ $data['appName'] }}</span>
</div>
<table class="table">
    <thead>
        <tr class="text-center">
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Vendor</th>
            <th scope="col">Product Type</th>
            <th scope="col">Status</th>
            <th scope="col">Number reviews</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['products'] as $index => $product)
        <tr class="text-center">
            <th scope="row">{{ $index + 1 }}</th>
            <td><a class="text-decoration-none link-review" value="{{ route('reviews.list', $product->id) }}">{{ \Illuminate\Support\Str::limit($product->title, 50) }}</a></td>
            <td>{{ $product->vendor }}</td>
            <td>{{ $product->product_type }}</td>
            <td>{{ $product->status }}</td>
            <td>{{ $product->reviews_count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
$(".link-review").click(function(){
    $("#table-content").html("Loading...");
    const urlReview = $(this).attr("value");
    $("#table-content").load(urlReview);
});

$("#back").click(function(){
    $("#table-content").html("Loading...");
    $("#table-content").load("{{ route('apps.list') }}");
})
</script>