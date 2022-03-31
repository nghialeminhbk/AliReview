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
            <th scope="col">Title</th>
            <th scope="col">Content</th>
            <th scope="col">Images</th>
            <th scope="col">Created at</th>
            <th scope="col">Store reply</th>
            <th scope="col">Store created at</th>
            <th scope="col">Number Like</th>
            <th scope="col">Number Dislike</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['reviews'] as $index => $review)
        <tr class="text-center">
            <th scope="row">{{ $index + 1 }}</th>
            <td>{{ $review['rate'] }}</td>
            <td>{{ $review['author_name'] }}</td>
            <td><img src="{{ $review['author_avt'] }}" alt="" width="50" height="50"></td>
            <td>{{ $review['title'] }}</td>
            <td>{{ \Illuminate\Support\Str::limit($review['content'], 30) }}</td>
            <td>
                <!-- @foreach($review['img'])
                    <img src="{{ $review['img'] }}" alt="No photo" width="50" height="50">
                @endforeach -->
            </td>
            <td>{{ $review['created_at'] }}</td>
            <td>{{ $review['store_reply'] }}</td>
            <td>{{ $review['store_created_at'] }}</td>
            <td>{{ $review['number_like'] }}</td>
            <td>{{ $review['number_dislike'] }}</td>
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