<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
  public function store(Request $request)
  {
    $params = $request->json()->all();
    $contnet = $params['message'];

    $id = Str::uuid();

    $file = $id->toString() . '.jpg';

    Message::create([
      'id' => $id,
      'content' => $contnet,
      'file_path' => $file,
    ]);

    return response()->json($id);
  }
}
