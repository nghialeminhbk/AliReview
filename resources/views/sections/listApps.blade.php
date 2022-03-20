<ul class="list-group w-25">
    @foreach($data['apps'] as $app)
    <li class="list-group-item d-flex justify-content-between align-items-center border border-dark mb-3">
        <a class="text-decoration-none link-product" value="{{ route('products.list', $app['id']) }}">
            {{ $app['shop_name'] }}
        </a>
        <span class="badge bg-success rounded-pill">{{ $app['products_count'] }}</span>
    </li>
    @endforeach
</ul>

<script>
$(".link-product").click(function(){
    $("#table-content").html("Loading...");
    const urlProduct = $(this).attr("value");
    $("#table-content").load(urlProduct);
});
</script>