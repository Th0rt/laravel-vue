<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
  public function store(Request $request)
  {
    $params = $request->json()->all();

    list(, $image) = explode(';', $params['image']);
    list(, $image) = explode(',', $image);
    $decodedImage = base64_decode($image);

    $contnet = $params['message'];

    $id = DB::transaction(
      function ()
      use ($decodedImage, $contnet) {
        $id = Str::uuid();

        $file = $id->toString() . '.jpg';

        Message::create([
          'id' => $id,
          'content' => $contnet,
          'file_path' => $file,
        ]);

        $isSuccess = Storage::disk('s3')->put($file, $decodedImage);
        if (!$isSuccess) {
          throw new Exception('Upalod file failed.');
        }

        Storage::disk('s3')->setVisibility($file, 'public');

        return $id;
      }
    );


    return response()->json($id);
  }

  public function show($id)
  {
    /** @var Message $message */
    $message = Message::find($id);

    return response()->json([
      'message' => $message->content,
      'url' => config('app.image.url') . '/' . $message->file_path
    ]);
  }
}
