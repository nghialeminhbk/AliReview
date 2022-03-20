<div class="mb-3 d-flex align-items-center">
    <button class="btn btn-info me-3" id="back"><<</button> <span class="fw-bold">{{ $data['productTitle'] }}</span>
</div>
@if(count($data['reviews']) > 0)
<table class="table">
    <thead>
        <tr class="text-center">
            <th scope="col">#</th>
            <th scope="col">Rate</th>
            <th scope="col">Author name</th>
            <th scope="col">Author avt</th>
            <th scope="col">Content</th>
            <th scope="col">Image</th>
            <th scope="col">Date</th>
            <th scope="col">Number Like</th>
            <th scope="col">Number Unlike</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['reviews'] as $index => $review)
        <tr class="text-center">
            <th scope="row">{{ $index + 1 }}</th>
            <td>{{ $review['rate'] }}</td>
            <td>{{ $review['author_name'] }}</td>
            <td><img src="{{ $review['author_avt'] }}" alt="" width="50" height="50"></td>
            <td>{{ \Illuminate\Support\Str::limit($review['content'], 30) }}</td>
            <td><img src="{{ $review['img'] }}" alt="No photo" width="50" height="50"></td>
            <td>{{ $review['date'] }}</td>
            <td>{{ $review['number_like'] }}</td>
            <td>{{ $review['number_unlike'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<h5>
    No reviews to display!
</h5>
@endif
<script>
$(".link-review").click(function(){
    $("#table-content").html("Loading...");
    const urlReview = $(this).attr("value");
    $("#table-content").load(urlReview);
});

$("#back").click(function(){
    $("#table-content").html("Loading...");
    $("#table-content").load("{{ route('products.list', $data['appId']) }}");
})
</script>