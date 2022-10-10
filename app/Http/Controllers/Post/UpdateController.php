<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();

        $images = $data['images'] ?? null;
        $imageIdsForDelete = $data['image_ids_for_delete'] ?? null;
        $imageUrlsForDelete = $data['image_urls_for_delete'] ?? null;
        unset(
            $data['images'],
            $data['image_ids_for_delete'],
            $data['image_urls_for_delete'],
        );

        $post->update($data);

        $currentPostImages = $post->images;
        if ($imageIdsForDelete) {
            foreach ($currentPostImages as $currentPostImage) {
                if (in_array($currentPostImage->id, $imageIdsForDelete)) {
                    Storage::disk('public')->delete($currentPostImage->path);
                    Storage::disk('public')->delete(str_replace('images/', 'images/th_', $currentPostImage->path));
                    $currentPostImage->delete();
                }
            }
        }

        if ($imageUrlsForDelete) {
            $removeStr = $request->root() . '/storage/';
            foreach ($imageUrlsForDelete as $imageUrlForDelete) {
                $path = str_replace($removeStr, '', $imageUrlForDelete);
                Storage::disk('public')->delete($path);
            }
        }

        if ($images) {
            foreach ($images as $image) {
                $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
                $filePath = Storage::disk('public')->putFileAs('/images', $image, $name);
                $previewName = 'th_' . $name;

                Image::create([
                    'path' => $filePath,
                    'url' => url('/storage/' . $filePath),
                    'preview_url' => url('/storage/images/' . $previewName),
                    "post_id" => $post->id,
                ]);

                InterventionImage::make($image)
                    ->fit(100, 100)
                    ->save(storage_path('app/public/images/' . $previewName));
            }
        }

        return response()->json([
            'message' => 'success',
        ]);
    }
}
