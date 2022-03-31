<?php

namespace App\Services;
use App\Models\Review;

class ReviewService 
{
    public function getReviewsByProductId($productId){
        return Review::where('product_id', $productId)->get();
    } 

    public function add(array $data){
        $review = new Review(); 
        $review->product_id = $data['productId'];
        $review->rate = $data['rate'];
        $review->author_name = $data['authorName'];
        $review->author_avt = $data['authorAvt'];
        $review->title = $data['title'];
        $review->content = $data['content'];
        $review->img = $data['img'];
        $review->created_at = $data['createdAt'];
        $review->store_reply = $data['storeReply'];
        $review->store_reply_created = $data['storeReplyCreated'];
        $review->number_like = $data['numberLike'];
        $review->number_dislike = $data['numberDislike'];
        $review->save();

        return $review->id;
    }
    
    public function delete($reviewId) : bool{
        $review = Review::find($reviewId);
        $review->delete();
        
        return true;
    }

}