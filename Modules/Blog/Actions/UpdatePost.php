<?php

namespace Modules\Blog\Actions;

class UpdatePost
{
    public static function update($request, $post)
    {
        $post->update($request->except(['image', 'status']));

        // status
        $post->update([
            'status' => $request->status == 'draft' ? 'draft' : 'published',
        ]);

        $image = $request->image;
        if ($image) {
            deleteImage($post->image);
            $url = uploadImage($image, 'post');
            $post->update(['image' => $url]);
        }

        return $post;
    }
}
